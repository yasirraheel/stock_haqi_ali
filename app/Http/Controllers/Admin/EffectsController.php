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
        $query = Effect::query();

        if ($request->has('s') && !empty($request->s)) {
            $search = $request->s;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $effects = $query->orderBy('id', 'desc')->paginate(20);
        $effects->getCollection()->transform(function ($effect) {
            $path = storage_path("app/public/effects/{$effect->id}.mp4");
            $effect->converted_bytes = file_exists($path) ? filesize($path) : null;
            return $effect;
        });
        $page_title = 'Effects Management';
        return view('admin.effects.index', compact('effects', 'page_title'));
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

        if (empty($ids)) {
            return response()->json([]);
        }

        $effects = Effect::whereIn('id', $ids)->get();
        $response = [];

        foreach ($effects as $effect) {
            $path = storage_path("app/public/effects/{$effect->id}.mp4");
            $convertedBytes = file_exists($path) ? filesize($path) : null;
            $convertedMb = $convertedBytes !== null ? number_format($convertedBytes / 1048576, 2) . ' MB' : null;

            $response[$effect->id] = [
                'id' => $effect->id,
                'status' => $effect->status,
                'process_percent' => $effect->process_percent ?? 0,
                'process_step' => $effect->process_step ?? '',
                'converted_mb' => $convertedMb,
                'processed_url' => $effect->processed_url
            ];
        }

        return response()->json($response);
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
}
