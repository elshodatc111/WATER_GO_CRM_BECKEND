<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\product\StoreProductRequest;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller{

    public function store(StoreProductRequest $request){
        $user = auth()->user();
        if ($user->role === 'director') {
            if ($user->company_id != $request->company_id) {
                return back()->with('error', 'Ruxsat yo‘q.');
            }
        }
        $imageName = 'product_' . Str::uuid() . '.' . $request->image->extension();
        $request->image->move(public_path('products/images'), $imageName);
        $bannerName = 'product_banner_' . Str::uuid() . '.' . $request->image_banner->extension();
        $request->image_banner->move(public_path('products/banners'), $bannerName);
        Product::create([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => 'products/images/' . $imageName,
            'image_banner' => 'products/banners/' . $bannerName,
            'created_by' => $user->id,
            'is_active' => true,
        ]);
        return back()->with('success', 'Mahsulot muvaffaqiyatli qo‘shildi.');
    }

    public function toggleProductStatus(Request $request){
        $request->validate(['id' => 'required|exists:products,id']);
        $product = Product::findOrFail($request->id);
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if ($user->role === 'director') {
                if ($product->company_id !== $user->company_id) {
                    return back()->with('error', 'Ruxsat yo‘q.');
                }
            } else {
                return back()->with('error', 'Ruxsat yo‘q.');
            }
        }
        $product->update(['is_active' => !$product->is_active]);
        return back()->with('success',$product->is_active ? 'Mahsulot aktivlashtirildi' : 'Mahsulot bloklandi' );
    }

    public function deleteProduct(Request $request){
        $request->validate(['id' => 'required|exists:products,id']);
        $product = Product::findOrFail($request->id);
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if ($user->role === 'director') {
                if ($product->company_id !== $user->company_id) {
                    return back()->with('error', 'Ruxsat yo‘q.');
                }
            } else {
                return back()->with('error', 'Ruxsat yo‘q.');
            }
        }
        $product->delete();
        return back()->with('success', 'Mahsulot o‘chirildi.');
    }

}
