<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LogActivityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        try {
            $response = $next($request);

            $responseContent = $response->getContent();
            $responseData = json_decode($responseContent, true);

            // ActivityLog::create([
            //     'user_id' => Auth::check() ? Auth::id() : null,
            //     'url' => $request->fullUrl(),
            //     'method' => $request->method(),
            //     'request_data' => json_encode($request->except(['password', 'password_confirmation'])),
            //     'response_data' => is_array($responseData) ? json_encode($responseData) : null,
            //     'ip_address' => $request->ip(),
            //     'status_code' => $response->status(),
            //     'error_message' => $responseData['message'] ?? null,
            // ]);

            return $response;
        } catch (Throwable $e) {

            // ActivityLog::create([
            //     'user_id' => Auth::check() ? Auth::id() : null,
            //     'url' => $request->fullUrl(),
            //     'method' => $request->method(),
            //     'request_data' => json_encode($request->except(['password', 'password_confirmation'])),
            //     'response_data' => null,
            //     'ip_address' => $request->ip(),
            //     'status_code' => 500,
            //     'error_message' => $e->getMessage(),
            // ]);

            throw $e;
        }
    }
}
