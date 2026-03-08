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

      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="card-title mb-0">Buyurtma #{{ $order->id }}</h5>
                <small class="text-muted">
                  Yaratilgan: {{ $order->created_at->format('d.m.Y H:i') }}
                  @if($order->delivered_at)
                    | Yetkazilgan: {{ $order->delivered_at->format('d.m.Y H:i') }}
                  @endif
                </small>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge 
                @if($order->status == 'pending') bg-warning text-dark
                @elseif($order->status == 'qabul_qilindi') bg-primary
                @elseif($order->status == 'yetkazilmoqda') bg-info text-dark
                @elseif($order->status == 'yetkazildi') bg-success
                @else bg-danger
                @endif
              ">
                @switch($order->status)
                  @case('pending') Yangi @break
                  @case('qabul_qilindi') Qabul qilindi @break
                  @case('yetkazilmoqda') Yetkazilmoqda @break
                  @case('yetkazildi') Yetkazildi @break
                  @case('canceled') Bekor qilingan @break
                  @default Nomaʼlum
                @endswitch
              </span>
                @if(in_array($order->status, ['pending','qabul_qilindi']))
                  <form action="{{ route('orders_cancel', $order->id) }}" method="POST" 
                        onsubmit="return confirm('Bu buyurtmani bekor qilishni tasdiqlaysizmi?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-x-circle"></i> Bekor qilish
                    </button>
                  </form>
                @endif
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                  <i class="bi bi-printer"></i> Chop etish
                </button>
              </div>
            </div>
            <p class="text-muted mb-2"></p>
            <hr class="mt-0">

            <div class="row">
              <div class="col-md-6">
                <h6 class="fw-bold">Mijoz maʼlumotlari</h6>
                <p class="mb-1"><strong>Ismi:</strong> {{ $order->user->name ?? '-' }}</p>
                <p class="mb-1"><strong>Telefon:</strong> {{ $order->user->phone ?? '-' }}</p>
                <p class="mb-1"><strong>Manzil:</strong> {{ $order->address }}</p>
                @if($order->latitude && $order->longitude)
                  <p class="mb-1">
                    <strong>Koordinatalar:</strong> {{ $order->latitude }}, {{ $order->longitude }}
                  </p>
                @endif
              </div>
              <div class="col-md-6">
                <h6 class="fw-bold">Firma va yetkazib beruvchi</h6>
                <p class="mb-1">
                  <strong>Firma:</strong> 
                  @if($order->company)
                    <a href="{{ route('companye_show', $order->company->id) }}">
                      {{ $order->company->company_name }}
                    </a>
                  @else
                    -
                  @endif
                </p>
                <p class="mb-1">
                  <strong>Kuryer:</strong> {{ optional($order->courier)->name ?? '-' }}
                </p>
                <p class="mb-1">
                  <strong>Toʻlov usuli:</strong> {{ $order->payment_method ?? '-' }}
                </p>
                <p class="mb-1">
                  <strong>Toʻlov holati:</strong> {{ $order->payment_status ?? '-' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Buyurtma tarkibi</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                  <tr class="text-center">
                    <th>#</th>
                    <th>Mahsulot</th>
                    <th>Miqdor</th>
                    <th>Narx</th>
                    <th>Jami</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($order->items as $item)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>
                        {{ $item->product->name ?? 'O‘chirilgan mahsulot' }}
                      </td>
                      <td class="text-center">{{ $item->quantity }}</td>
                      <td class="text-end">{{ number_format($item->price, 0, '.', ' ') }}</td>
                      <td class="text-end fw-semibold">{{ number_format($item->total, 0, '.', ' ') }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted">Buyurtma tarkibi topilmadi.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Umumiy maʼlumot</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between">
                <span>Buyurtma soni:</span>
                <span class="fw-bold">{{ $order->total_count }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Mahsulotlar summasi:</span>
                <span class="fw-bold">{{ number_format($order->total_price, 0, '.', ' ') }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Yetkazib berish narxi:</span>
                <span class="fw-bold">{{ number_format($order->delivery_price, 0, '.', ' ') }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Jami summa:</span>
                <span class="fw-bold text-primary">{{ number_format($order->final_price, 0, '.', ' ') }}</span>
              </li>
            </ul>
          </div>
        </div>

        @if($order->courier_comment || $order->courier_rating || $order->courier_rating_text)
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Kuryer fikri / bahosi</h5>
              @if($order->courier_rating)
                <p class="mb-1">
                  <strong>Bahosi:</strong> {{ $order->courier_rating }} / 5
                </p>
              @endif
              @if($order->courier_rating_text)
                <p class="mb-1">
                  <strong>Izoh:</strong> {{ $order->courier_rating_text }}
                </p>
              @endif
              @if($order->courier_comment)
                <p class="mb-0">
                  <strong>Sistemadagi izoh:</strong> {{ $order->courier_comment }}
                </p>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>
  </section>

@endsection