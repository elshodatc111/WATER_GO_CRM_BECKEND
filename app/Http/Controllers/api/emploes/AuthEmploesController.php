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
use OpenApi\Attributes as OA;

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
    #[OA\Post(
        path: "/v1/employees/login",
        summary: "Direktor yoki kuryer uchun login",
        description: "Xodim (director/courier) telefon va parol orqali tizimga kiradi. Faqat kompaniyaga biriktirilgan va aktiv bo‘lgan xodimlarga ruxsat beriladi.",
        tags: ["Xodim – Autentifikatsiya"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["phone","password"],
                properties: [
                    new OA\Property(
                        property: "phone",
                        type: "string",
                        example: "+998901234567",
                        description: "Xodim telefon raqami"
                    ),
                    new OA\Property(
                        property: "password",
                        type: "string",
                        example: "password",
                        description: "Xodim paroli"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Kirish muvaffaqiyatli, token va user maʼlumotlari qaytarildi"
            ),
            new OA\Response(
                response: 401,
                description: "Telefon yoki parol noto‘g‘ri"
            ),
            new OA\Response(
                response: 403,
                description: "Hisob bloklangan, o‘chirilgan yoki kompaniya faol emas"
            )
        ]
    )]
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
    #[OA\Get(
        path: "/v1/employees/profile",
        summary: "Joriy xodim profilini olish",
        security: [["sanctum" => []]],
        tags: ["Xodim – Profil"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Xodim va uning kompaniyasi haqidagi qisqa maʼlumotlar"
            ),
            new OA\Response(
                response: 401,
                description: "Token noto‘g‘ri yoki yuborilmagan"
            )
        ]
    )]
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
    #[OA\Post(
        path: "/v1/employees/logout",
        summary: "Xodimni tizimdan chiqarish",
        security: [["sanctum" => []]],
        tags: ["Xodim – Autentifikatsiya"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sessiya yopildi, token bekor qilindi"
            )
        ]
    )]
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
    #[OA\Post(
        path: "/v1/employees/session-check",
        summary: "Sessiyani tekshirish (1 qurilma siyosati)",
        description: "Berilgan FCM token bo‘yicha boshqa foydalanuvchi sessiyalarini yopib, joriy foydalanuvchi uchun bitta aktiv qurilma qoldiradi.",
        security: [["sanctum" => []]],
        tags: ["Xodim – Autentifikatsiya"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["fcm_token"],
                properties: [
                    new OA\Property(
                        property: "fcm_token",
                        type: "string",
                        example: "fcm-token-here",
                        description: "Qurilmaning Firebase Cloud Messaging tokeni"
                    ),
                    new OA\Property(
                        property: "device_type",
                        type: "string",
                        nullable: true,
                        example: "android",
                        description: "Qurilma turi: android yoki ios"
                    ),
                    new OA\Property(
                        property: "device_name",
                        type: "string",
                        nullable: true,
                        example: "Samsung A54",
                        description: "Qurilmaning nomi"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Sessiya muvaffaqiyatli yangilandi va bog‘landi"
            ),
            new OA\Response(
                response: 403,
                description: "Hisob bloklangan, o‘chirilgan yoki kompaniya faol emas"
            )
        ]
    )]
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