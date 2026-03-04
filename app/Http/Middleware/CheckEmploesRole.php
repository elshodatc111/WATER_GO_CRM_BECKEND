<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmploesRole{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response{
        $user = $request->user();
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Sizda bu amalni bajarish uchun ruxsat yo‘q.'
            ], 403);
        }
        if (method_exists($user, 'trashed') && $user->trashed()) {
            $user->tokens()->delete(); // Sessiyani yopish
            return response()->json([
                'success' => false,
                'message' => 'Hisob o‘chirilgan.'
            ], 403);
        }
        if (!$user->is_active) {
            $user->tokens()->delete();
            return response()->json([
                'success' => false,
                'message' => 'Hisob bloklangan.'
            ], 403);
        }
        if (!$user->company || !$user->company->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Kompaniya faol emas.'
            ], 403);
        }
        return $next($request);
    }
}