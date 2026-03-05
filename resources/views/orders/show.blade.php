@extends('layouts.admin')
@section('title', 'Buyurtma haqida')
@section('content')
  <div class="pagetitle">
    <h1>Buyurtma haqida</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('orders_new') }}">Yangi buyurtmalar</a></li>
        <li class="breadcrumb-item active">Buyurtma haqida</li>
      </ol>
    </nav>
  </div>
  <section class="section dashboard">
    <div class="row">

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Buyurtma haqida</h5>
            <p>Siz tizimga muvaffaqiyatli kirdingiz. Bu yerda sizning asosiy statistikalaringiz ko'rinadi.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection