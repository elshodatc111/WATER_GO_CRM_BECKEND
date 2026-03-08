<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrderController extends Controller{
    protected $notificationService;

    public function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService;
    }
    
    #[OA\Post(
        path: "/v1/user/orders",
        summary: "Yangi buyurtma yaratish",
        description: "Mijoz tanlangan firma va mahsulotlar bo‘yicha yangi buyurtma yaratadi. Xizmat haqi firma balansidan ushlab qolinadi.",
        security: [["sanctum" => []]],
        tags: ["Mijoz – Buyurtmalar"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["company_id","address","items"],
                properties: [
                    new OA\Property(
                        property: "company_id",
                        type: "integer",
                        example: 1,
                        description: "Buyurtma yuborilayotgan firma ID si"
                    ),
                    new OA\Property(
                        property: "address",
                        type: "string",
                        example: "Toshkent shahri, Chilonzor tumani, 1-daha",
                        description: "Yetkazib berish manzili"
                    ),
                    new OA\Property(
                        property: "latitude",
                        type: "number",
                        format: "float",
                        nullable: true,
                        example: 41.3111,
                        description: "Manzil koordinatasi (ixtiyoriy)"
                    ),
                    new OA\Property(
                        property: "longitude",
                        type: "number",
                        format: "float",
                        nullable: true,
                        example: 69.2797,
                        description: "Manzil koordinatasi (ixtiyoriy)"
                    ),
                    new OA\Property(
                        property: "payment_method",
                        type: "string",
                        nullable: true,
                        example: "cash",
                        description: "To‘lov usuli (masalan: cash)"
                    ),
                    new OA\Property(
                        property: "items",
                        type: "array",
                        description: "Buyurtma tarkibidagi mahsulotlar ro‘yxati",
                        items: new OA\Items(
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "product_id",
                                    type: "integer",
                                    example: 5,
                                    description: "Mahsulot ID si"
                                ),
                                new OA\Property(
                                    property: "quantity",
                                    type: "integer",
                                    example: 2,
                                    description: "Mahsulot soni"
                                ),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Buyurtma muvaffaqiyatli qabul qilindi"
            ),
            new OA\Response(
                response: 422,
                description: "Valdatsiya xatosi"
            ),
            new OA\Response(
                response: 500,
                description: "Server xatosi"
            )
        ]
    )]
    public function store(Request $request){
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'address'    => 'required|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'items'      => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);
        try {
            return DB::transaction(function () use ($request) {
                $company = Company::findOrFail($request->company_id);
                $totalPrice = 0;
                $totalCount = 0;
                $itemsToInsert = [];
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $lineTotal = $product->price * $item['quantity'];
                    $totalPrice += $lineTotal;
                    $totalCount += $item['quantity'];
                    $itemsToInsert[] = [
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $product->price,
                        'total'      => $lineTotal,
                    ];
                }
                $order = Order::create([
                    'user_id'        => $request->user()->id,
                    'company_id'     => $request->company_id,
                    'total_count'    => $totalCount,
                    'total_price'    => $totalPrice,
                    'delivery_price' => 0,
                    'final_price'    => $totalPrice,
                    'status'         => 'pending',
                    'payment_method' => $request->payment_method ?? 'cash',
                    'address'        => $request->address,
                    'latitude'       => $request->latitude,
                    'longitude'      => $request->longitude,
                ]);
                $order->items()->createMany($itemsToInsert);
                $service_fee = $company->service_fee;
                $company->decrement('balance', $service_fee);
                $this->notificationService->handleOrderStatusNotification($order->id, 'pending');
                return response()->json([
                    'status'  => true,
                    'message' => 'Buyurtma muvaffaqiyatli qabul qilindi',
                    'order'   => $order->load('items')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/v1/user/orders",
        summary: "Mijozning barcha buyurtmalari ro‘yxati",
        security: [["sanctum" => []]],
        tags: ["Mijoz – Buyurtmalar"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Buyurtmalar ro‘yxati muvaffaqiyatli qaytarildi"
            )
        ]
    )]
    public function index(Request $request){
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)->with(['company', 'items.product'])->orderby('id','desc')->get();
        return OrderResource::collection($orders);
    }

    #[OA\Get(
        path: "/v1/user/orders/{id}",
        summary: "Mijozning bitta buyurtmasi haqida maʼlumot",
        security: [["sanctum" => []]],
        tags: ["Mijoz – Buyurtmalar"],
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
                description: "Buyurtma topildi va maʼlumotlar qaytarildi"
            ),
            new OA\Response(
                response: 404,
                description: "Buyurtma topilmadi yoki ushbu mijozga tegishli emas"
            )
        ]
    )]
    public function show($id){
        $order = Order::where('user_id', auth()->id())->with(['company', 'items.product'])->findOrFail($id);
        return new OrderResource($order);
    }

    #[OA\Post(
        path: "/v1/user/orders/{id}/cancel",
        summary: "Mijoz tomonidan buyurtmani bekor qilish",
        description: "Faqat holati `pending` bo‘lgan buyurtmalarni bekor qilish mumkin. Firma xizmat haqi balansga qaytariladi.",
        security: [["sanctum" => []]],
        tags: ["Mijoz – Buyurtmalar"],
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
                description: "Buyurtma bekor qilindi va xizmat haqi qaytarildi"
            ),
            new OA\Response(
                response: 404,
                description: "Buyurtma topilmadi yoki bekor qilib bo‘lmaydi"
            )
        ]
    )]
    public function cancel(Request $request, $id){
        $order = Order::where('user_id', $request->user()->id)->where('status', 'pending')->findOrFail($id);
        return DB::transaction(function () use ($order) {
            $order->update(['status' => 'canceled']);
            $order->company->increment('balance', $order->company->service_fee);
            $this->notificationService->handleOrderStatusNotification($order->id, 'canceled');
            return response()->json([
                'status' => true,
                'message' => 'Buyurtma bekor qilindi, xizmat haqi balansingizga qaytarildi.'
            ]);
        });
    }

}
