<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LutApiController extends Controller
{
    /**
     * LUTs are stored with the existing Stock Effect submission flow using a
     * category such as "LUT". This keeps the upload workflow unchanged while
     * giving Reel2Reel a native LUT library.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $query = $this->eligibleQuery()->where(function ($q) {
            $q->whereRaw('LOWER(category) LIKE ?', ['%lut%'])
                ->orWhereRaw('LOWER(effect_url) LIKE ?', ['%.cube%'])
                ->orWhereRaw('LOWER(effect_url) LIKE ?', ['%.3dl%']);
        });

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $luts = $query->orderByDesc('id')->get();
        $formatted = $luts->map(function ($lut) {
            return [
                'lut_id' => (int) $lut->id,
                'title' => $lut->title ?: 'Stock LUT',
                'description' => $lut->description ?? '',
                'category' => $lut->category ?? 'LUT',
                'download_url' => route('api.v1.luts.download', $lut->id),
                'format' => $this->formatFromUrl($lut->effect_url),
            ];
        })->values()->all();

        return response()->json([
            'status' => 200,
            'success' => true,
            'total_luts' => count($formatted),
            'LUTS_LIST' => $formatted,
            'data' => $formatted,
        ], 200, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, X-API-KEY, Authorization',
        ]);
    }

    public function download($id)
    {
        $lut = $this->eligibleQuery()->where('id', (int) $id)->first();
        if (!$lut || !$this->isLut($lut)) {
            return response()->json(['message' => 'LUT not found'], 404, [
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        $sourceUrl = $lut->effect_url;
        if (preg_match('/(?:id=|file\/d\/|\/d\/)([a-zA-Z0-9_-]+)/', $sourceUrl, $match)) {
            $sourceUrl = 'https://drive.usercontent.google.com/download?id=' . $match[1] . '&export=download&confirm=t';
        }

        $format = $this->formatFromUrl($lut->effect_url);
        return response()->stream(function () use ($sourceUrl) {
            $curl = curl_init($sourceUrl);
            curl_setopt_array($curl, [
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_CONNECTTIMEOUT => 20,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_USERAGENT => 'Reel2Reel Stock LUT Proxy',
                CURLOPT_WRITEFUNCTION => static function ($handle, $chunk) {
                    echo $chunk;
                    flush();
                    return strlen($chunk);
                },
            ]);
            curl_exec($curl);
            curl_close($curl);
        }, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'inline; filename="' . addslashes(($lut->title ?: 'stock-lut') . '.' . $format) . '"',
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'no-store',
        ]);
    }

    private function eligibleQuery()
    {
        return DB::table('effects')->where(function ($q) {
            $q->where('is_active', true)
                ->orWhere(function ($submission) {
                    $submission->whereNotNull('added_by')
                        ->where(function ($status) {
                            $status->whereNull('status')
                                ->orWhere('status', '!=', 'blocked');
                        });
                });
        })->whereNotNull('effect_url')->where('effect_url', '!=', '');
    }

    private function isLut($lut): bool
    {
        return stripos((string) $lut->category, 'lut') !== false
            || preg_match('/\.(cube|3dl)(?:[?#].*)?$/i', (string) $lut->effect_url) === 1;
    }

    private function formatFromUrl(?string $url): string
    {
        if (preg_match('/\.(cube|3dl)(?:[?#].*)?$/i', (string) $url, $match)) {
            return strtolower($match[1]);
        }
        return 'cube';
    }
}
