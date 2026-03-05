@extends('layouts.admin')
@section('title', 'Completed Orders')
@section('content')
  <div class="pagetitle">
    <h1>Yakunlangan buyurtmalar</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Yakunlangan buyurtmalar</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Buyurtmalar tarixi</h5>
            <form action="{{ route('orders_end') }}" method="GET" class="w-25">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control" placeholder="Tarixdan qidirish..." id="searchInput">
            </form>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover" style="font-size: 14px">
            <thead>
              <tr class="text-center bg-light">
                <th>#</th>
                <th>Firma</th>
                <th>Buyurtmachi</th>
                <th>Holat</th>
                <th>Manzil</th>
                <th>Buyurtma soni</th>
                <th>Jami narx</th>
                <th>Buyurtma vaqti</th>
                <th>Yakunlangan vaqt</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $item)
              <tr>
                <td class="text-center">{{ ($orders->currentPage()-1) * $orders->perPage() + $loop->iteration }}</td>
                <td><a href="{{ route('orders_show',$item->id) }}">{{ $item->company->company_name }}</a></td>
                <td>{{ $item->user->name }}</td>
                <td class="text-center">
                    @if($item->status == 'yetkazildi')
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Yetkazildi</span>
                    @else
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Bekor qilindi</span>
                    @endif
                </td>
                <td>{{ $item->address }}</td>
                <td class="text-center">{{ $item->total_count }}</td>
                <td class="text-center"><strong>{{ number_format($item->total_price, 0, '.', ' ') }}</strong></td>
                <td class="text-center">{{ $item->created_at->format('d.m.Y H:i') }}</td>
                <td class="text-center">{{ $item->updated_at->format('d.m.Y H:i') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-3">Tarixda ma'lumotlar mavjud emas.</td>
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

    // Avtomatik fokus va kursorni oxiriga qo'yish
    if (searchInput.value !== "") {
        searchInput.focus();
        let val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;
    }
  </script>
@endsection