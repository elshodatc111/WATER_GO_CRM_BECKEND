<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller{
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

    public function profile(Request $request){
        $user = $request->user();
        return new UserResource($user);
    }

    public function checkToken(Request $request){
        return response()->json([
            'status' => true,
            'message' => 'Token aktiv',
            'user' => new UserResource($request->user())
        ], 200);
    }

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
