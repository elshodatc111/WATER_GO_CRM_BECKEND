@extends('layouts.admin')
@section('title', 'Firma maxsuloti haqida')
@section('content')
  <div class="pagetitle">
    <h1>Firma maxsuloti haqida</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companyee') }}">Firmalar</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companye_show',$product['company_id']) }}">Firma haqida</a></li>
        <li class="breadcrumb-item active">Firma maxsuloti haqida</li>
      </ol>
    </nav>
  </div>
  <section class="section dashboard">
    <div class="row">
      <div class="col-lg-4">
        <div class="card">
          <div class="position-relative">
            <img src="{{ asset($product['image_banner']) }}" class="w-100 rounded-top">
            <div class="position-absolute" style="bottom:-30px; left:20px;">
              <img src="{{ asset($product['image']) }}" style="height:70px;width:70px;object-fit:cover;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.2);">
            </div>
          </div>
          <div class="card-body mt-4">
            <div class="d-flex justify-content-between align-items-center mb-0 pb-0">
                <h5 class="card-title mb-0 pb-1">
                    {{ $product['name'] }}
                </h5>
                <span class="badge bg-success mb-0 pb-1">
                    {{ $product['price'] }} UZS
                </span>
            </div>
            <hr class="my-2">
            <div class="row mb-1">
              <div class="col-6">
                <small class="text-muted">Maxsulot Holati</small>
                <div class="fw-semibold">{{ $product['is_active'] }}</div>
              </div>
              <div class="col-6">
                <small class="text-muted">Maxsulot Yaratdi</small>
                <div class="fw-semibold">{{ $product->creator->name }}</div>
              </div>
              <div class="col-6">
                <small class="text-muted">Maxsulot yaratilgan vaqt</small>
                <div class="fw-semibold">{{ $product['created_at'] }}</div>
              </div>
              <div class="col-6">
                <small class="text-muted">Oxirgi yangilanish</small>
                <div class="fw-semibold">{{ $product['updated_at'] }}</div>
              </div>
              <hr class="my-2">
              <div class="col-12">
                <small class="text-muted">Maxsulot haqida</small>
                <div class="fw-semibold">{{ $product['description'] }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Maxsulotni yangilash</h5>
            <form action="{{ route('product_update') }}" method="post">
              @csrf 
              <input type="hidden" name="id" value="{{ $product['id'] }}">
              <label for="name" class="mb-2">Maxsulot nomi</label>
              <input type="text" name="name" required class="form-control" value="{{ $product['name'] }}">
              <label for="description" class="my-2">Maxsulot haqida</label>
              <textarea name="description" required class="form-control">{{ $product['description'] }}</textarea>
              <label for="price" class="my-2">Maxsulot narxi</label>
              <input type="text" name="price" class="form-control" id="amount2"  value="{{ $product['price'] }}" required>
              <button type="submit" class="btn btn-outline-primary w-100 mt-3">Yangilashni saqlash</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Maxsulot rasmini yangilash</h5>
            <form action="{{ route('product_update_image') }}" method="post" enctype="multipart/form-data">
              @csrf  
              <input type="hidden" name="id" value="{{ $product['id'] }}">
              <label for="image" class="my-2">Maxsulot rasmi(256x256)</label>
              <input type="file" name="image" required class="form-control">
              <button type="submit" class="btn btn-outline-primary w-100 mt-3">Rasmni saqlash</button>
            </form>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Maxsulot bannerni yangilash</h5>
            <form action="{{ route('product_update_banner') }}" method="post" enctype="multipart/form-data">
              @csrf 
              <input type="hidden" name="id" value="{{ $product['id'] }}">
              <label for="image_banner" class="my-2">Maxsulot banner(1080x512)</label>
              <input type="file" name="image_banner" required class="form-control">
              <button type="submit" class="btn btn-outline-primary w-100 mt-3">Bannerni saqlash</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection