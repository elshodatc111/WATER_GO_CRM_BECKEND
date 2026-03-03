<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\product\StoreProductRequest;
use App\Http\Requests\web\product\UpdateProductBannerRequest;
use App\Http\Requests\web\product\UpdateProductImageRequest;
use App\Http\Requests\web\product\UpdateProductRequest;
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

    public function show($id){
        $product = Product::findOrFail($id);
        return view('company.product_show',compact('product'));
    }

    public function update(UpdateProductRequest $request){
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
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);
        return back()->with('success', 'Mahsulot muvaffaqiyatli yangilandi.');
    }

    public function updateImage(UpdateProductImageRequest $request){
        $product = Product::findOrFail($request->id);
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if ($user->role === 'director') {
                if ($product->company_id !== $user->company_id) { return back()->with('error', 'Ruxsat yo‘q.'); }
            } else { return back()->with('error', 'Ruxsat yo‘q.'); }
        }
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        $file = $request->file('image');
        $fileName = 'product_' . Str::uuid() . '.' . $file->extension();
        $file->move(public_path('products/images'), $fileName);
        $product->update([ 'image' => 'products/images/' . $fileName ]);
        return back()->with('success', 'Mahsulot rasmi yangilandi.');
    }

    public function updateBanner(UpdateProductBannerRequest $request){
        $product = Product::findOrFail($request->id);
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if ($user->role === 'director') {
                if ($product->company_id !== $user->company_id) { return back()->with('error', 'Ruxsat yo‘q.'); }
            } else { return back()->with('error', 'Ruxsat yo‘q.'); }
        }
        if ($product->image_banner && file_exists(public_path($product->image_banner))) {
            unlink(public_path($product->image_banner));
        }
        $file = $request->file('image_banner');
        $fileName = 'product_banner_' . Str::uuid() . '.' . $file->extension();
        $file->move(public_path('products/banners'), $fileName);
        $product->update([
            'image_banner' => 'products/banners/' . $fileName
        ]);
        return back()->with('success', 'Mahsulot banneri yangilandi.');
    }

}
