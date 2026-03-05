@extends('layouts.admin')
@section('title', 'Active Orders')
@section('content')
  <div class="pagetitle">
    <h1>Aktiv buyurtmalar</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Aktiv buyurtmalar</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Faol jarayondagi buyurtmalar</h5>
            <form action="{{ route('orders_active') }}" method="GET" class="w-25">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control" placeholder="Qidirish..." id="searchInput">
            </form>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered" style="font-size: 14px">
            <thead>
              <tr class="text-center bg-light">
                <th>#</th>
                <th>Firma</th>
                <th>Buyurtmachi</th>
                <th>Holat (Status)</th> 
                <th>Manzil</th>
                <th>Buyurtma soni</th>
                <th>Narxi</th>
                <th>Vaqti</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $item)
              <tr>
                <td class="text-center">{{ ($orders->currentPage()-1) * $orders->perPage() + $loop->iteration }}</td>
                <td><a href="{{ route('orders_show',$item->id) }}">{{ $item->company->company_name }}</a></td>
                <td>{{ $item->user->name }}</td>
                <td class="text-center">
                    @if($item->status == 'qabul_qilindi')
                        <span class="badge bg-primary text-white">Qabul qilindi</span>
                    @else
                        <span class="badge bg-warning text-dark">Yetkazilmoqda</span>
                    @endif
                </td>
                <td>{{ $item->address }}</td>
                <td class="text-center">{{ $item->total_count }}</td>
                <td class="text-center">{{ number_format($item->total_price, 0, '.', ' ') }}</td>
                <td class="text-center">{{ $item->created_at->format('d.m.Y H:i') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">Aktiv buyurtmalar mavjud emas.</td>
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
    let searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        clearTimeout(this.delay);
        this.delay = setTimeout(function() {
            this.form.submit();
        }.bind(this), 800);
    });
    // Kursor har doim input oxirida turishi uchun
    searchInput.focus();
    let val = searchInput.value;
    searchInput.value = '';
    searchInput.value = val;
  </script>
@endsection