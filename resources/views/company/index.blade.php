@extends('layouts.admin')
@section('title', 'Firmalar')
@section('content')
  <div class="pagetitle">
    <h1>Firmalar</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Firmalar</li>
      </ol>
    </nav>
  </div>
  <section class="section dashboard">
    <div class="row">

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-9">
                <h5 class="card-title">Suv firmalar</h5>
              </div>
              <div class="col-lg-3" style="text-align: right">
                <button class="btn btn-outline-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#yangi_firma">
                  <i class="bi bi-plus"></i> Yangi Firma
                </button>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered" style="font-size: 14px">
                <thead>
                  <tr class="text-center">
                    <th>#</th>
                    <th>Firma</th>
                    <th>Drektor</th>
                    <th>Xodimlar somi</th>
                    <th>Maxsulotlar soni</th>
                    <th>Firma Balansi</th>
                    <th>Buyurtma uchun to'lov</th>
                    <th>Reyting</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($companyee as $item)
                  <tr>
                    <td class="text-center">{{ $loop->index + 1 }}</td>
                    <td><a href="{{ route('companye_show', $item->id) }}">{{ $item->company_name }}</a></td>
                    <td>{{ $item->direktor }}</td>
                    <td class="text-center">0</td>
                    <td class="text-center">0</td>
                    <td class="text-center">{{ number_format($item->balance, 2, '.', ' ') }}</td>
                    <td class="text-center">{{ number_format($item->service_fee, 2, '.', ' ') }}</td>
                    <td class="text-center">{{ $item->rating }} ({{ $item->rating_count }})</td>
                    <td class="text-center">
                      @if($item->is_active == 1)
                        Aktiv
                      @else
                        Bloklangan
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center">Fermalar mavjud emas.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<div class="modal" id="yangi_firma" tabindex="-1">
  <form action="{{ route('companye_create') }}" method="post" enctype="multipart/form-data">
    @csrf 
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Yangi ferma qo'shish</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label for="company_name">Firma nomi</label>
          <input type="text" name="company_name" class="form-control" required>
          <label for="direktor" class="mt-2">Firma raxbari</label>
          <input type="text" name="direktor" class="form-control" required>
          <div class="row">
            <div class="col-6">
              <label for="phone" class="mt-2">Firma telefon raqami</label>
              <input type="text" name="phone" class="form-control phone" value="+998" required>
            </div>
            <div class="col-6">
              <label for="working_hours" class="mt-2">Ish vaqti</label>
              <input type="text" name="working_hours" class="form-control work_time" value="08:00-20:00" required>
            </div>
            <div class="col-12">
              <label for="address" class="mt-2">Firma manzili</label>
              <input type="text" name="address" class="form-control" required>
            </div>
            <div class="col-6">
              <label for="service_fee" class="mt-2">Xizmat narxi</label>
              <input type="text" name="service_fee" id="amount" class="form-control" required>
            </div>
            <div class="col-6">              
              <label for="inn" class="mt-2">INN</label>
              <input type="text" name="inn" class="form-control inn" required>
            </div>
            <div class="col-6">
              <label for="logo" class="mt-2">Logotip (256x256, jpg)</label>
              <input type="file" name="logo" class="form-control" required>
            </div>
            <div class="col-6">              
              <label for="banner" class="mt-2">Banner (1920x1080px, jpg)</label>
              <input type="file" name="banner" class="form-control" required>
            </div>
            <div class="col-6">
              <label for="latitude" class="mt-2">Latude</label>
              <input type="text" name="latitude" required class="form-control long_lat">
            </div>
            <div class="col-6">              
              <label for="longitude" class="mt-2">Longtude</label>
              <input type="text" name="longitude" required class="form-control long_lat">
            </div>
            <div class="col-12">              
              <label for="delivery_radius" class="mt-2">Yetqazish radusi (km)</label>
              <input type="number" name="delivery_radius" required class="form-control">
            </div>
          </div>
          <label for="description" class="mt-2">Firma haqida</label>
          <textarea name="description" required class="form-control"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
          <button type="submit" class="btn btn-primary">Saqlash</button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection