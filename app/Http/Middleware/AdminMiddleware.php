<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware{

    public function handle(Request $request, Closure $next): Response{
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['error' => 'Akkaunt faol emas']);
        }
        if ($user->role !== 'admin') {
            abort(403, 'Ruxsat yo‘q');
        }
        return $next($request);
    }
    
}
