<?php

namespace App\Http\Controllers\Api\Emploes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Emploes\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AuthEmploesController extends Controller{
    /**
     * Foydalanuvchi holatini tekshirish uchun yordamchi metod
     */
    private function checkUserStatus($user): ?JsonResponse{
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Foydalanuvchi topilmadi'], 404);
        }
        if ($user->trashed()) {
            return response()->json(['success' => false, 'message' => 'Hisob o‘chirilgan'], 403);
        }
        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'Hisob bloklangan'], 403);
        }
        if (!$user->company || !$user->company->is_active) {
            return response()->json(['success' => false, 'message' => 'Kompaniya faol emas yoki topilmadi'], 403);
        }
        return null;
    }
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(LoginRequest $request): JsonResponse{
        // SoftDelete bo'lganlarni ham tekshirish uchun withTrashed() kerak
        $user = User::withTrashed()->with('company')->where('phone', $request->phone)->first();
        // 1. Statuslarni tekshirish
        $statusError = $this->checkUserStatus($user);
        if ($statusError) return $statusError;
        // 2. Parolni tekshirish
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Telefon yoki parol noto‘g‘ri'], 401);
        }
        // 3. Rolni tekshirish
        if (!in_array($user->role, ['director', 'courier'])) {
            return response()->json(['success' => false, 'message' => 'Bu hisob mobile uchun ruxsatga ega emas'], 403);
        }
        $token = $user->createToken('mobile_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login muvaffaqiyatli',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
                'company_id' => $user->company_id,
            ]
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    public function profile(): JsonResponse    {
        $user = auth()->user()->loadMissing('company');        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
                'company' => [
                    'id' => $user->company->id ?? null,
                    'name' => $user->company->company_name ?? null,
                    'is_active' => $user->company->is_active ?? null,
                ]
            ]
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(): JsonResponse{
        auth()->user()->currentAccessToken()?->delete();        
        return response()->json([
            'success' => true,
            'message' => 'Tizimdan chiqildi'
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | SESSION CHECK (1 DEVICE POLICY)
    |--------------------------------------------------------------------------
    */
    public function sessionCheck(Request $request): JsonResponse    {
        $request->validate([
            'fcm_token'   => 'required|string',
            'device_type' => 'nullable|in:android,ios',
            'device_name' => 'nullable|string'
        ]);
        $user = auth()->user()->loadMissing('company');
        $statusError = $this->checkUserStatus($user);
        if ($statusError) {
            $user->tokens()->delete(); // Bloklangan bo'lsa barcha sessiyalarni yopish
            return $statusError;
        }
        DB::transaction(function () use ($request, $user) {
            $existingDevice = UserDevice::where('fcm_token', $request->fcm_token)->first();
            if ($existingDevice && $existingDevice->user_id !== $user->id) {
                $existingDevice->user?->tokens()->delete();
                $existingDevice->delete();
            }
            UserDevice::where('user_id', $user->id)->delete();
            UserDevice::create([
                'user_id'     => $user->id,
                'fcm_token'   => $request->fcm_token,
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
            ]);
        });
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
                'company' => [
                    'id' => $user->company->id ?? null,
                    'name' => $user->company->company_name ?? null,
                    'is_active' => $user->company->is_active ?? null,
                ]
            ]
        ]);
    }
}