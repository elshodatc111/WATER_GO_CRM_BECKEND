<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class AuthController extends Controller{
    #[OA\Post(
        path: "/v1/user/auth/send-otp",
        summary: "Mijozga OTP SMS yuborish",
        description: "Berilgan telefon raqami bo‘yicha mijozni yaratadi (agar mavjud bo‘lmasa) va unga bir martalik tasdiqlash kodi (OTP) yuboradi. Hozircha demo rejimda 123456 kod ishlatiladi.",
        tags: ["Mijoz – Autentifikatsiya"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["phone"],
                properties: [
                    new OA\Property(
                        property: "phone",
                        type: "string",
                        example: "+998901234567",
                        description: "Mijozning telefon raqami, xalqaro formatda (+998...)"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Kod yuborildi"
            ),
            new OA\Response(
                response: 422,
                description: "Valdatsiya xatosi (telefon kiritilmagan yoki noto‘g‘ri format)"
            )
        ]
    )]
    public function sendOtp(Request $request){
        $request->validate(['phone' => 'required|string']);
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => 'Mijoz', // Default ism
                'role' => 'client',
                'is_active' => true
            ]
        );
        #$code = rand(100000, 999999); 
        $code = 123456;
        $user->update([
            'sms_code_hash' => Hash::make($code),
            'sms_code_expires_at' => now()->addMinutes(5),
        ]);
        return response()->json(['status'=>true,'message' => 'Kod yuborildi'],200);
    }

    #[OA\Post(
        path: "/v1/user/auth/verify-otp",
        summary: "OTP kodni tasdiqlash va token olish",
        description: "Mijoz kiritgan SMS kodi to‘g‘ri bo‘lsa, unga Sanctum access token qaytaradi. Shuningdek FCM token va qurilma maʼlumotlari ham saqlanishi mumkin.",
        tags: ["Mijoz – Autentifikatsiya"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["phone","code"],
                properties: [
                    new OA\Property(
                        property: "phone",
                        type: "string",
                        example: "+998901234567",
                        description: "Mijoz telefon raqami"
                    ),
                    new OA\Property(
                        property: "code",
                        type: "string",
                        example: "123456",
                        description: "SMS orqali yuborilgan 6 xonali kod"
                    ),
                    new OA\Property(
                        property: "fcm_token",
                        type: "string",
                        nullable: true,
                        example: "fcm-token-here",
                        description: "Push bildirishnomalar uchun Firebase token (ixtiyoriy)"
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
                        description: "Qurilmaning nomi (modeli)"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token va foydalanuvchi maʼlumotlari muvaffaqiyatli qaytarildi"
            ),
            new OA\Response(
                response: 422,
                description: "Kod xato yoki muddati tugagan"
            )
        ]
    )]
    public function verifyOtp(Request $request){
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
            'fcm_token' => 'nullable|string',
            'device_type' => 'nullable|in:android,ios',
            'device_name' => 'nullable|string',
        ]);
        $user = User::where('phone', $request->phone)->firstOrFail();
        if (Carbon::now()->isAfter($user->sms_code_expires_at) || !Hash::check($request->code, $user->sms_code_hash)) {
            return response()->json(['error' => 'Kod xato yoki muddati o‘tgan'], 422);
        }
        $user->update(['sms_code_hash' => null,'sms_code_expires_at' => null,'phone_verified_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;
        if ($request->fcm_token) {
            $user->devices()->updateOrCreate(
                ['fcm_token' => $request->fcm_token],
                [
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name,
                ]
            );
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ]
        ],200);
    }

    #[OA\Get(
        path: "/v1/user/profile",
        summary: "Joriy mijoz profilini olish",
        security: [["sanctum" => []]],
        tags: ["Mijoz – Profil"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Mijoz profili maʼlumotlari"
            ),
            new OA\Response(
                response: 401,
                description: "Token noto‘g‘ri yoki yuborilmagan"
            )
        ]
    )]
    public function profile(Request $request){
        $user = $request->user();
        return new UserResource($user);
    }

    #[OA\Get(
        path: "/v1/user/auth/check",
        summary: "Token holatini tekshirish",
        description: "Berilgan Bearer token orqali foydalanuvchi hali aktiv ekanligini tekshiradi.",
        security: [["sanctum" => []]],
        tags: ["Mijoz – Autentifikatsiya"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Token aktiv va foydalanuvchi maʼlumotlari qaytarildi"
            ),
            new OA\Response(
                response: 401,
                description: "Token noto‘g‘ri yoki muddati tugagan"
            )
        ]
    )]
    public function checkToken(Request $request){
        return response()->json([
            'status' => true,
            'message' => 'Token aktiv',
            'user' => new UserResource($request->user())
        ], 200);
    }

    #[OA\Post(
        path: "/v1/user/profile/update",
        summary: "Mijoz profilini yangilash",
        security: [["sanctum" => []]],
        tags: ["Mijoz – Profil"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Ali Valiyev",
                        description: "Yangi ism, kamida 3 ta harf"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Profil muvaffaqiyatli yangilandi"
            ),
            new OA\Response(
                response: 422,
                description: "Valdatsiya xatosi"
            )
        ]
    )]
    public function updateProfile(Request $request){
        $request->validate([
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Ismni kiritish shart!',
            'name.min' => 'Ism kamida 3 ta harfdan iborat bo\'lishi kerak.',
        ]);
        $user = $request->user();
        $user->update([
            'name' => $request->name
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Profil muvaffaqiyatli yangilandi',
            'user' => new UserResource($user)
        ],200);
    }
    


}
