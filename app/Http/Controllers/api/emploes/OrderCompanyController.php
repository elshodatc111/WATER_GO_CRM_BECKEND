<?php

namespace App\Http\Controllers\api\emploes;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrderCompanyController extends Controller{
    protected $notificationService;

    public function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService;
    }
    #[OA\Get(
        path: "/v1/employees/orders",
        summary: "Kompaniya buyurtmalarini ko‘rish (direktor/kuryer)",
        description: "Direktor barcha kompaniya buyurtmalarini status bo‘yicha filtrlashi mumkin. Kuryer faqat o‘zi olishi mumkin bo‘lgan va o‘zi biriktirilgan aktiv buyurtmalarni ko‘radi.",
        security: [["sanctum" => []]],
        tags: ["Xodim – Buyurtmalar"],
        parameters: [
            new OA\Parameter(
                name: "status",
                in: "query",
                required: false,
                description: "Faqat direktor uchun: buyurtma statusi bo‘yicha filter (pending, qabul_qilindi, yetkazilmoqda, yetkazildi, canceled)",
                schema: new OA\Schema(type: "string", example: "pending")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Buyurtmalar ro‘yxati muvaffaqiyatli qaytarildi"
            ),
            new OA\Response(
                response: 403,
                description: "Ruxsatsiz foydalanuvchi"
            )
        ]
    )]
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
    #[OA\Post(
        path: "/v1/employees/orders/{id}/approve",
        summary: "Direktor tomonidan buyurtmani tasdiqlash",
        description: "Holati pending bo‘lgan buyurtmani direktor qabul qiladi (`qabul_qilindi`). Shundan so‘ng kuryer uni olish imkoniyatiga ega bo‘ladi.",
        security: [["sanctum" => []]],
        tags: ["Xodim – Buyurtmalar"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Buyurtma ID si",
                schema: new OA\Schema(type: "integer", example: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Buyurtma tasdiqlandi"
            ),
            new OA\Response(
                response: 404,
                description: "Buyurtma topilmadi yoki tasdiqlab bo‘lmaydi"
            )
        ]
    )]
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
    #[OA\Post(
        path: "/v1/employees/orders/{id}/take",
        summary: "Kuryer tomonidan buyurtmani qabul qilish",
        description: "Holati `qabul_qilindi` bo‘lgan va hali kuryer biriktirilmagan buyurtmani kuryer (yoki direktor) o‘ziga oladi va holat `yetkazilmoqda` ga o‘zgaradi.",
        security: [["sanctum" => []]],
        tags: ["Xodim – Buyurtmalar"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Buyurtma ID si",
                schema: new OA\Schema(type: "integer", example: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Buyurtma kuryerga biriktirildi"
            ),
            new OA\Response(
                response: 403,
                description: "Kuryer uchun ruxsat yo‘q"
            ),
            new OA\Response(
                response: 404,
                description: "Buyurtma topilmadi yoki allaqachon band qilingan"
            )
        ]
    )]
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
    #[OA\Post(
        path: "/v1/employees/orders/{id}/complete",
        summary: "Buyurtmani yakunlash (yetkazildi deb belgilash)",
        description: "Kuryer o‘zi biriktirilgan va holati `yetkazilmoqda` bo‘lgan buyurtmani yetkazildi deb belgilaydi.",
        security: [["sanctum" => []]],
        tags: ["Xodim – Buyurtmalar"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Buyurtma ID si",
                schema: new OA\Schema(type: "integer", example: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Buyurtma muvaffaqiyatli yakunlandi"
            ),
            new OA\Response(
                response: 404,
                description: "Buyurtma topilmadi yoki kuryerga tegishli emas"
            )
        ]
    )]
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
    #[OA\Post(
        path: "/v1/employees/orders/{id}/cancel",
        summary: "Direktor tomonidan buyurtmani bekor qilish",
        description: "Direktor pending, qabul_qilindi yoki yetkazilmoqda holatidagi buyurtmani bekor qilishi mumkin. Sabab `reason` maydonida yuboriladi.",
        security: [["sanctum" => []]],
        tags: ["Xodim – Buyurtmalar"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Buyurtma ID si",
                schema: new OA\Schema(type: "integer", example: 10)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["reason"],
                properties: [
                    new OA\Property(
                        property: "reason",
                        type: "string",
                        example: "Mijoz buyurtmani bekor qildi",
                        description: "Bekor qilish sababi (maks. 255 belgi)"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Buyurtma direktor tomonidan bekor qilindi"
            ),
            new OA\Response(
                response: 403,
                description: "Faqat direktor ushbu amalni bajara oladi"
            ),
            new OA\Response(
                response: 404,
                description: "Buyurtma topilmadi yoki bekor qilib bo‘lmaydi"
            )
        ]
    )]
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
