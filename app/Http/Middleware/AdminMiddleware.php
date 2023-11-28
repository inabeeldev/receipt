<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('token') ? $request->header('token') :  $request->token;
        $ad = Admin::firstWhere('auth_token', $token);

        if (isset($ad)) {
            return $next($request);
        }
        $errors = [];
        $errors[] = ['code' => 'auth-001', 'message' => 'Admin not found'];
        return response()->json([
            'errors' => $errors
        ], 401);
    }
}
