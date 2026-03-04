<?php

namespace App\Http\Controllers\api\emploes;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class OrderCompanyController extends Controller{
    protected $notificationService;

    public function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService;
    }
    // Barcha buyurtmalar
    public function adminOrders(Request $request){
        $user = $request->user();
        $query = Order::with(['user', 'items.product', 'courier'])->where('company_id', $user->company_id);
        if ($user->role === 'director') {
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
        } 
        elseif ($user->role === 'courier') {
            $query->where(function($q) use ($user) {
                $q->where('status', 'qabul_qilindi')->whereNull('courier_id')
                ->orWhere(function($sub) use ($user) {
                    $sub->where('courier_id', $user->id)->whereIn('status', ['yetkazilmoqda', 'qabul_qilindi']);
                });
            });
        } else {
            return response()->json(['message' => 'Ruxsat berilmagan'], 403);
        }
        $orders = $query->latest()->paginate(50);
        return OrderResource::collection($orders);
    }
    // Buyurtmani tasdiqlash
    public function approveOrderByDirector(Request $request, $id){
        $order = Order::where('company_id', $request->user()->company_id)->where('status', 'pending')->findOrFail($id);
        $order->update(['status' => 'qabul_qilindi']);
        $this->notificationService->handleOrderStatusNotification($order->id, 'qabul_qilindi');
        return response()->json([
            'status' => true,
            'message' => 'Buyurtma tasdiqlandi. Endi kuryerlar uni qabul qilishi mumkin.',
            'order' => new OrderResource($order)
        ]);
    }
    // Xaydovchi qabul qilishi
    public function takeOrder(Request $request, $id){
        $user = $request->user();
        if (!in_array($user->role, ['courier', 'director'])) {
            return response()->json([
                'status' => false,
                'message' => 'Sizda buyurtmani qabul qilish huquqi yo\'q.'
            ], 403);
        }
        $order = Order::where('company_id', $user->company_id)->where('status', 'qabul_qilindi')->whereNull('courier_id')->findOrFail($id);
        $order->update([
            'courier_id' => $user->id,
            'status' => 'yetkazilmoqda'
        ]);
        $this->notificationService->handleOrderStatusNotification($order->id, 'yetkazilmoqda');
        return response()->json([
            'status' => true,
            'message' => 'Buyurtma muvaffaqiyatli biriktirildi. Oq yo\'l!',
            'order' => new OrderResource($order)
        ]);
    }
    // Buyurtmani yakunlash
    public function completeOrder(Request $request, $id){
        $user = $request->user();
        $order = Order::where('company_id', $user->company_id)->where('courier_id', $user->id)->where('status', 'yetkazilmoqda')->findOrFail($id);
        $order->update([
            'status' => 'yetkazildi',
            'payment_status' => 'success', // Pul olindi deb hisoblaymiz
            'delivered_at' => now(),       // Yetkazilgan vaqtni muhrlaymiz
        ]);
        $this->notificationService->handleOrderStatusNotification($order->id, 'yetkazildi');
        return response()->json([
            'status' => true,
            'message' => 'Buyurtma muvaffaqiyatli yakunlandi. Rahmat!',
            'order' => new OrderResource($order)
        ]);
    }
    // Buyurtmani bekor qilish
    public function cancelOrderByDirector(Request $request, $id){
        if (!in_array($request->user()->role, ['director'])) {
            return response()->json(['message' => 'Ruxsat berilmagan'], 403);
        }
        $request->validate([
            'reason' => 'required|string|max:255' 
        ]);
        $order = Order::where('company_id', $request->user()->company_id)->whereIn('status', ['pending', 'qabul_qilindi', 'yetkazilmoqda'])->findOrFail($id);
        $order->update([
            'status' => 'canceled',
            'courier_comment' => $request->reason,
        ]);
        $this->notificationService->handleOrderStatusNotification($order->id, 'canceled');
        return response()->json([
            'status' => true,
            'message' => 'Buyurtma direktor tomonidan bekor qilindi.',
            'order' => new OrderResource($order)
        ]);
    }

}
