<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class NotificationService{
    public function handleOrderStatusNotification($orderId, $status){
        $order = Order::with(['user', 'company'])->find($orderId);
        if (!$order) return;
        switch ($status) {
            case 'pending':
                // Yangi buyurtma kelganda: Firma direktorlariga xabar yuborish
                $this->notifyDirectors($order);
                break;
            case 'qabul_qilindi':
                // Direktor tasdiqlasa: Mijozga xabar
                $this->sendToUser($order->user_id, "Buyurtma tasdiqlandi", "Sizning #{$order->id} buyurtmangiz tasdiqlandi.");
                $this->notifyCurrers($order);
                break;
            case 'yetkazilmoqda':
                // Kuryer qabul qilsa: Mijozga xabar
                $this->sendToUser($order->user_id, "Kuryer yo'lga chiqdi", "Kuryer buyurtmangizni oldi va yo'lga chiqdi.");
                break;
            case 'yetkazildi':
                // Yakunlanganda: Mijozga xabar
                $this->sendToUser($order->user_id, "Yetkazildi", "Suv yetkazib berildi. Rahmat!");
                break;
            case 'canceled':
                // Bekor qilinganda: Mijozga xabar
                $this->sendToUser($order->user_id, "Bekor qilindi", "Buyurtmangiz bekor qilindi.");
                break;
        }
    }
    /**
     * Kompaniyaning barcha direktorlarini topib xabar yuborish
     */
    private function notifyDirectors($order){
        $directors = User::where('company_id', $order->company_id)->where('role', 'director')->get();
        foreach ($directors as $director) {
            $this->sendToUser($director->id, "Yangi buyurtma!", "Kompaniyangizga yangi #{$order->id} buyurtma keldi.");
        }
    }
    /**
    * Kompaniyaning barcha direktorlarini topib xabar yuborish
    */
    private function notifyCurrers($order){
        $directors = User::where('company_id', $order->company_id)->where('role', 'courier')->get();
        foreach ($directors as $director) {
            $this->sendToUser($director->id, "Yangi buyurtma!", "Kompaniyangizga yangi #{$order->id} buyurtma keldi.");
        }
    }
    /**
     * Umumiy yuborish metodi (Hozircha Logga)
     */
    private function sendToUser($userId, $title, $body){
        Log::info("--- [NOTIFICATION] --- To User ID: {$userId} | Title: {$title} | Body: {$body}");
    }
}