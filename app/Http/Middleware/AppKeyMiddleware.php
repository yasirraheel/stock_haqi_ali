<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Handle CORS OPTIONS preflight requests
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'X-API-KEY, Content-Type, Authorization, X-Requested-With');
        }

        // Get CUSTOM_API_KEY from environment (separate from Laravel's APP_KEY)
        $app_key = env('CUSTOM_API_KEY', 'cineworm_stock_api_key_2026');

        // Get API key from header or request parameter
        $api_key = $request->header('X-API-KEY') ?: $request->input('api_key');

        if (!$api_key) {
            return response()->json([
                'error' => 'Missing API key',
                'message' => 'Please provide API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ], 401)->header('Access-Control-Allow-Origin', '*');
        }

        // Validate the API key against APP_KEY
        if ($api_key !== $app_key) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'Provided API key is invalid',
                'status_code' => 401
            ], 401)->header('Access-Control-Allow-Origin', '*');
        }

        $response = $next($request);
        if (method_exists($response, 'header')) {
            $response->header('Access-Control-Allow-Origin', '*')
                     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                     ->header('Access-Control-Allow-Headers', 'X-API-KEY, Content-Type, Authorization, X-Requested-With');
        }

        return $response;
    }
}
