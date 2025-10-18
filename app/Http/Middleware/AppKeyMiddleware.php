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
        // Get APP_KEY from environment
        $app_key = env('APP_KEY');

        if (empty($app_key)) {
            return response()->json([
                'error' => 'Configuration error',
                'message' => 'APP_KEY not configured on server',
                'status_code' => 500
            ], 500);
        }

        // Get API key from header or request parameter
        $api_key = $request->header('X-API-KEY') ?: $request->input('api_key');

        if (!$api_key) {
            return response()->json([
                'error' => 'Missing API key',
                'message' => 'Please provide API key in X-API-KEY header or api_key parameter',
                'status_code' => 401
            ], 401);
        }

        // Validate the API key against APP_KEY
        if ($api_key !== $app_key) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'Provided API key is invalid',
                'status_code' => 401
            ], 401);
        }

        return $next($request);
    }
}
