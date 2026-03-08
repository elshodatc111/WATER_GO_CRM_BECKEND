<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller{
    
    public function cancel(Request $request, $id){
        $order = Order::findOrFail($id);
        if (!in_array($order->status, ['pending','qabul_qilindi'])) {
            return back()->with('error', 'Faqat yangi yoki qabul qilingan buyurtmalarni bekor qilish mumkin.');
        }
        $order->update([
            'status' => 'canceled',
        ]);
        return back()->with('success', 'Buyurtma bekor qilindi.');
    }

    public function new(Request $request){
        $search = $request->input('search');
        $orders = Order::with(['company','user'])
            ->where('status', 'pending')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('address', 'like', "%{$search}%")
                    ->orWhere('total_price', 'like', "%{$search}%")
                    ->orWhereHas('user', function($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('company', function($qc) use ($search) {
                        $qc->where('company_name', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        return view('orders.new', compact('orders'));
    }
    # 'pending','qabul_qilindi','canceled','yetkazilmoqda','yetkazildi'
    public function active(Request $request){
        $search = $request->input('search');

        $orders = Order::with(['company','user'])
            ->whereIn('status', ['qabul_qilindi', 'yetkazilmoqda']) // 'wherein' emas 'whereIn'
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('address', 'like', "%{$search}%")
                    ->orWhere('total_price', 'like', "%{$search}%")
                    ->orWhereHas('user', function($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('company', function($qc) use ($search) {
                        $qc->where('company_name', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('orders.active', compact('orders'));
    }

    public function end(Request $request) {
        $search = $request->input('search');

        $orders = Order::with(['company','user'])
            ->whereIn('status', ['canceled', 'yetkazildi']) 
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('address', 'like', "%{$search}%")
                    ->orWhere('total_price', 'like', "%{$search}%")
                    ->orWhereHas('user', function($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('company', function($qc) use ($search) {
                        $qc->where('company_name', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('orders.end', compact('orders'));
    }

    public function show($id){
        $order = Order::with(['company','user','courier','items.product'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

}
