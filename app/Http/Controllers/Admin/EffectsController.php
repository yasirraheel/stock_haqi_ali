<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Effect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EffectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['checkStatus']);
    }

    public function index(Request $request)
    {
        $filter = $request->input('status', 'all');
        $sort = $request->input('sort', 'processing_first');

        $query = Effect::query();

        if ($request->has('s') && !empty($request->s)) {
            $search = $request->s;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($filter === 'ready') {
            $query->where('status', 'ready');
        } elseif ($filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filter === 'processing') {
            $query->whereIn('status', ['downloading', 'processing']);
        } elseif ($filter === 'failed') {
            $query->where(function($q) {
                $q->whereIn('status', ['failed', 'error'])
                  ->orWhere(function($q2) {
                      $q2->where('status', '!=', 'ready')
                         ->where(function($q3) {
                             $q3->whereNull('processed_url')->orWhere('processed_url', '');
                         })
                         ->whereNotIn('status', ['pending', 'downloading', 'processing']);
                  });
            });
        }

        // Sorting order: top to bottom processing order or newest/oldest
        if ($sort === 'asc') {
            // Queue FIFO: processing order top to bottom
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'desc') {
            // Newest ID first
            $query->orderBy('id', 'desc');
        } else {
            // Default: processing_first -> Active downloading/processing first, then Pending (FIFO 1 -> 9999), then Failed, then Ready
            $query->orderByRaw("
                CASE 
                    WHEN status = 'downloading' THEN 1
                    WHEN status = 'processing' THEN 2
                    WHEN status = 'pending' THEN 3
                    WHEN status in ('failed', 'error') THEN 4
                    ELSE 5
                END ASC, id ASC
            ");
        }

        $effects = $query->paginate(20)->appends($request->except('page'));
        $effects->getCollection()->transform(function ($effect) {
            $path = storage_path("app/public/effects/{$effect->id}.mp4");
            $effect->converted_bytes = file_exists($path) ? filesize($path) : null;
            return $effect;
        });

        // Global status counts for filter badges
        $statusCounts = Effect::selectRaw("
            count(*) as total,
            sum(case when status = 'ready' then 1 else 0 end) as ready,
            sum(case when status in ('downloading', 'processing') then 1 else 0 end) as processing,
            sum(case when status = 'pending' then 1 else 0 end) as pending,
            sum(case when status in ('failed', 'error') then 1 else 0 end) as failed
        ")->first();

        // Currently active running effect
        $activeEffect = Effect::whereIn('status', ['downloading', 'processing'])->first(['id', 'title', 'status', 'process_step', 'process_percent']);

        $page_title = 'Effects Management';
        return view('admin.effects.index', compact('effects', 'page_title', 'filter', 'sort', 'statusCounts', 'activeEffect'));
    }

    public function checkStatus(Request $request)
    {
        $idsRaw = $request->input('ids');
        if (is_array($idsRaw)) {
            $ids = $idsRaw;
        } elseif (is_string($idsRaw) && strlen(trim($idsRaw)) > 0) {
            $ids = array_filter(explode(',', $idsRaw));
        } else {
            $ids = [];
        }

        $items = [];
        if (!empty($ids)) {
            $effects = Effect::whereIn('id', $ids)->get();
            foreach ($effects as $effect) {
                $path = storage_path("app/public/effects/{$effect->id}.mp4");
                $convertedBytes = file_exists($path) ? filesize($path) : null;
                $convertedMb = $convertedBytes !== null ? number_format($convertedBytes / 1048576, 2) . ' MB' : null;

                $items[$effect->id] = [
                    'id' => $effect->id,
                    'status' => $effect->status,
                    'process_percent' => $effect->process_percent ?? 0,
                    'process_step' => $effect->process_step ?? '',
                    'converted_mb' => $convertedMb,
                    'processed_url' => $effect->processed_url
                ];
            }
        }

        // Global status counts
        $counts = Effect::selectRaw("
            count(*) as total,
            sum(case when status = 'ready' then 1 else 0 end) as ready,
            sum(case when status in ('downloading', 'processing') then 1 else 0 end) as processing,
            sum(case when status = 'pending' then 1 else 0 end) as pending,
            sum(case when status in ('failed', 'error') then 1 else 0 end) as failed
        ")->first();

        // Currently active running effect
        $activeEffect = Effect::whereIn('status', ['downloading', 'processing'])->first(['id', 'title', 'status', 'process_step', 'process_percent']);

        return response()->json([
            'items' => $items,
            'counts' => [
                'total' => (int)($counts->total ?? 0),
                'ready' => (int)($counts->ready ?? 0),
                'processing' => (int)($counts->processing ?? 0),
                'pending' => (int)($counts->pending ?? 0),
                'failed' => (int)($counts->failed ?? 0)
            ],
            'active_effect' => $activeEffect
        ]);
    }

    public function create()
    {
        $page_title = 'Add New Effect';
        return view('admin.effects.create', compact('page_title'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'effect_url' => 'required|string',
            'category' => 'nullable|string',
            'license_price' => 'nullable|numeric|min:0',
            'is_premium' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $effect_url = trim($request->effect_url);

        // Convert Google Drive share link if provided
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $effect_url, $matches)) {
            $fileId = $matches[1];
            $effect_url = "https://drive.google.com/uc?export=download&id={$fileId}";
        }

        $is_premium = (int)$request->input('is_premium', 0) === 1;
        $license_price = $is_premium && $request->license_price ? round((float)$request->license_price, 2) : 0;

        $effect = new Effect();
        $effect->title = $request->title;
        $effect->description = $request->description;
        $effect->effect_url = $effect_url;
        $effect->category = $request->category ?: 'General';
        $effect->license_price = $license_price;
        $effect->is_premium = $is_premium;
        $effect->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
        $effect->status = 'pending';

        $effect->save();

        \App\Jobs\ProcessStockEffectJob::dispatch($effect->id);

        return redirect()->route('admin.effects.index')
            ->with('flash_message', 'Effect created and queued for processing successfully.');
    }

    public function edit($id)
    {
        $effect = Effect::findOrFail($id);
        $page_title = 'Edit Effect';
        return view('admin.effects.edit', compact('effect', 'page_title'));
    }

    public function update(Request $request, $id)
    {
        $effect = Effect::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'effect_url' => 'required|string',
            'category' => 'nullable|string',
            'license_price' => 'nullable|numeric|min:0',
            'is_premium' => 'nullable',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $effect_url = trim($request->effect_url);
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $effect_url, $matches)) {
            $fileId = $matches[1];
            $effect_url = "https://drive.google.com/uc?export=download&id={$fileId}";
        }

        $is_premium = (int)$request->input('is_premium', 0) === 1;
        $license_price = $is_premium && $request->license_price ? round((float)$request->license_price, 2) : 0;

        $urlChanged = $effect->getOriginal('effect_url') !== $effect_url;
        $needsProcessing = $urlChanged || empty($effect->processed_url);
        
        $effect->title = $request->title;
        $effect->description = $request->description;
        $effect->effect_url = $effect_url;
        $effect->category = $request->category ?: 'General';
        $effect->license_price = $license_price;
        $effect->is_premium = $is_premium;
        $effect->is_active = $request->has('is_active') ? (bool)$request->is_active : true;

        if ($needsProcessing) {
            $effect->status = 'pending';
        }

        $effect->save();

        if ($needsProcessing) {
            \App\Jobs\ProcessStockEffectJob::dispatch($effect->id);
            $message = 'Effect updated and queued for processing successfully.';
        } else {
            $message = 'Effect updated successfully.';
        }

        return redirect()->route('admin.effects.index')
            ->with('flash_message', $message);
    }

    public function destroy($id)
    {
        $effect = Effect::findOrFail($id);
        $effect->delete();

        return redirect()->route('admin.effects.index')
            ->with('flash_message', 'Effect deleted successfully.');
    }

    public function retryFailed(Request $request)
    {
        $failedEffects = Effect::where(function($q) {
            $q->whereIn('status', ['failed', 'error', 'pending'])
              ->orWhereNull('processed_url')
              ->orWhere('processed_url', '');
        })->where('status', '!=', 'ready')->get();

        $count = 0;
        foreach ($failedEffects as $effect) {
            $effect->status = 'pending';
            $effect->process_step = 'Queued for processing...';
            $effect->process_percent = 0;
            $effect->save();

            \App\Jobs\ProcessStockEffectJob::dispatch($effect->id);
            $count++;
        }

        return redirect()->back()->with('flash_message', "Successfully re-queued {$count} effects for background processing!");
    }

    public function retrySingle($id)
    {
        $effect = Effect::findOrFail($id);
        $effect->status = 'pending';
        $effect->process_step = 'Queued for processing...';
        $effect->process_percent = 0;
        $effect->save();

        \App\Jobs\ProcessStockEffectJob::dispatch($effect->id);

        return redirect()->back()->with('flash_message', "Effect #{$effect->id} ({$effect->title}) re-queued for processing!");
    }
}
