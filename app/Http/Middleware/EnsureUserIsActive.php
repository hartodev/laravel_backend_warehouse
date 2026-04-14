<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user = $request->user();
 
        if ($user && ! $user->is_active) {
            // Logout session jika web
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                auth()->logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
            }
 
            // Revoke token jika API
            $user->currentAccessToken()?->delete();
 
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ], 403);
        }
 
        return $next($request);
    }
}
