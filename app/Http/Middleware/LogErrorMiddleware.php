<?php

namespace App\Http\Middleware;

use App\Models\ErrorLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogErrorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            dd('Middleware LogErrorMiddleware berjalan');
            // Simpan error ke database
            ErrorLog::create([
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'ip_address' => $request->ip(),
                'user_id' => Auth::check() ? Auth::id() : null,
            ]);

            // Kembalikan response error
            return response()->json(['error' => 'Terjadi kesalahan!'], 500);
        }
    }
}
