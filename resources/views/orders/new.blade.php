@extends('layouts.admin')
@section('title', 'New Order')
@section('content')
  <div class="pagetitle">
    <h1>Yangi buyurtmalar</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Yangi buyurtmalar</li>
      </ol>
    </nav>
  </div>

  <section class="section dashboard">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Yangi buyurtmalar</h5>
            <form action="{{ route('orders_new') }}" method="GET" class="w-25">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control" placeholder="Qidirish..." id="searchInput">
            </form>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered" style="font-size: 14px" id="ordersTable">
            <thead>
              <tr class="text-center">
                <th>#</th>
                <th>Firma</th>
                <th>Buyurtmachi</th>
                <th>Buyurtma manzili</th>
                <th>Buyurtma soni</th>
                <th>Buyurtma narxi</th>
                <th>Buyurtma vaqti</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $item)
              <tr>
                <td class="text-center">{{ ($orders->currentPage()-1) * $orders->perPage() + $loop->iteration }}</td>
                <td><a href="{{ route('orders_show',$item->id) }}">{{ $item->company->company_name }}</a></td>
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->address }}</td>
                <td class="text-center">{{ $item->total_count }}</td>
                <td class="text-center">{{ number_format($item->total_price, 0, '.', ' ') }}</td>
                <td class="text-center">{{ $item->created_at->format('d.m.Y H:i') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan=7 class="text-center">Buyurtmalar mavjud emas.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </section>

  <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        // Agar foydalanuvchi yozishdan to'xtasa, avtomatik formani yuboradi
        clearTimeout(this.delay);
        this.delay = setTimeout(function() {
            this.form.submit();
        }.bind(this), 800); // 0.8 soniya kutish
    });
  </script>
@endsection