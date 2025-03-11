<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDisabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        
        if(auth()->check() && auth()->user()->disabled_at){
            return response([
                'disabled' => true,
                'message' => config("mitd.auth.error.disabled"),
            ], 401);
        }

        return $next($request);
    }
}
