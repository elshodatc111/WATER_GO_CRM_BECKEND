<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller{
    
    public function new(Request $request){
        $search = $request->input('search');
        $orders = Order::where('status', 'pending')
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

        $orders = Order::whereIn('status', ['qabul_qilindi', 'yetkazilmoqda']) // 'wherein' emas 'whereIn'
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

        $orders = Order::whereIn('status', ['canceled', 'yetkazildi']) 
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
        return view('orders.show');
    }

}
