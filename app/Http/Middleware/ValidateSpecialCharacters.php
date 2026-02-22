<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ValidateSpecialCharacters
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya jalankan filter jika request adalah POST
        if ($request->isMethod('post')) {
            $rules = [];
            $messages = [];
            $input = $request->all();

            foreach ($input as $key => $value) {
                if (is_string($value)) {
                    $rules[$key] = 'filled|string|not_regex:/[\'"`~]/';
                    $messages["$key.not_regex"] = ucfirst(str_replace('_', ' ', $key)) . " tidak boleh mengandung karakter (' \" ` ~)";
                }
            }

            // Validasi request
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return new JsonResponse([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }
        }

        return $next($request);
    }
}
