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

class OrderController extends Controller{
    protected $notificationService;

    public function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService;
    }
    
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
                $company = Company::findOrFail($request->company_id);
                $service_fee = $company->service_fee;
                $company->balance = $company->balance - $service_fee;
                $company->save();
                $company->decrement('balance', $company->service_fee);
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

    public function index(Request $request){
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)->with(['company', 'items.product'])->orderby('id','desc')->get();
        return OrderResource::collection($orders);
    }

    public function show($id){
        $order = Order::where('user_id', auth()->id())->with(['company', 'items.product'])->findOrFail($id);
        return new OrderResource($order);
    }

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
