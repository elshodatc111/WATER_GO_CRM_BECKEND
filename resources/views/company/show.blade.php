@extends('layouts.admin')
@section('title', 'Firma haqida')
@section('content')
  <div class="pagetitle">
    <h1>Firma haqida</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companyee') }}">Firmalar</a></li>
        <li class="breadcrumb-item active">Firma haqida</li>
      </ol>
    </nav>
  </div>
  <section class="section dashboard">
    <div class="row">
      <div class="col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="position-relative">
            <img src="{{ asset($company['banner']) }}" class="w-100 rounded-top">
            <div class="position-absolute" style="bottom:-30px; left:20px;">
              <img src="{{ asset($company['logo']) }}" style="height:70px;width:70px;object-fit:cover;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.2);">
            </div>
            <div class="position-absolute top-0 end-0 m-3">
              <span class="badge bg-white text-dark fs-6">
                ⭐ {{ number_format($company['rating'], 1) }}
                ({{ $company['rating_count'] }})
              </span>
            </div>
          </div>
          <div class="card-body mt-4">
            <div class="d-flex justify-content-between align-items-center mb-0 pb-0">
                <h5 class="card-title mb-0 pb-1">
                    {{ $company['company_name'] }}
                </h5>
                <span class="badge bg-success mb-0 pb-1">
                    {{ $company['delivery_radius'] }} KM
                </span>
            </div>
            <hr class="m-0 mb-2">
            <div class="row text-center mb-0">
              <div class="col-6">
                <h6 class="text-muted">Balans</h6>
                <h5 class="fw-bold text-primary">
                    {{ number_format($company['balance'], 0, ',', ' ') }} so‘m
                </h5>
              </div>
              <div class="col-6">
                <h6 class="text-muted">Xizmat narxi</h6>
                <h5 class="fw-bold text-danger">
                    {{ number_format($company['service_fee'], 0, ',', ' ') }} so‘m
                </h5>
              </div>
            </div>
            <hr class="mt-0 pt-0">
            <div class="row mb-1">
              <div class="col-6">
                <small class="text-muted">Direktor</small>
                <div class="fw-semibold">{{ $company['direktor'] }}</div>
              </div>
              <div class="col-6">
                <small class="text-muted">Telefon</small>
                <div class="fw-semibold">{{ $company['phone'] }}</div>
              </div>
            </div>
            <div class="row mb-1">
              <div class="col-6">
                <small class="text-muted">INN</small>
                <div class="fw-semibold">{{ $company['inn'] }}</div>
              </div>
              <div class="col-6">
                <small class="text-muted">Ish vaqti</small>
                <div class="fw-semibold">{{ $company['working_hours'] }}</div>
              </div>
            </div>
            <div class="row mb-1">
              <div class="col-6">
                <small class="text-muted">Latitude</small>
                <div>{{ $company['latitude'] }}</div>
              </div>
              <div class="col-6">
                <small class="text-muted">Longitude</small>
                <div>{{ $company['longitude'] }}</div>
              </div>
            </div>
            <div class="row mb-1">
              <div class="col-6">
                <small class="text-muted">Manzil</small>
                <div>{{ $company['address'] }}</div>
              </div>
              <div class="col-6">
                <small class="text-muted">Ish faoliyati</small>
                <div>
                  @if($company['is_active'])
                    <span class="badge bg-success">
                      <i class="bi bi-check-circle me-1"></i> Firma faol
                    </span>
                  @else
                    <span class="badge bg-danger">
                      <i class="bi bi-x-circle me-1"></i> Faoliyat yakunlangan
                    </span>
                  @endif
                </div>
              </div>
            </div>
            <hr class="m-0 p-0">
            <div>
              <p class="mt-1 mb-0 description-scroll">
                  {{ $company['description'] }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title mb-0 pb-0">Firma malulotlarini yangilash</h2>
            <form action="{{ route('companye_update') }}" method="post" enctype="multipart/form-data">
              @csrf 
              <input type="hidden" name="id" value="{{ $company['id'] }}">
              <label for="company_name">Firma nomi</label>
              <input type="text" name="company_name" value="{{ $company['company_name'] }}" class="form-control" required>
              <label for="direktor" class="mt-2 mb-2">Firma raxbari</label>
              <input type="text" name="direktor" class="form-control" value="{{ $company['direktor'] }}" required>
              <div class="row">
                <div class="col-6">
                  <label for="phone" class="mt-2 mb-2">Firma telefon raqami</label>
                  <input type="text" name="phone" class="form-control phone" value="{{ $company['phone'] }}" required>
                </div>
                <div class="col-6">
                  <label for="working_hours" class="mt-2 mb-2">Ish vaqti</label>
                  <input type="text" name="working_hours" class="form-control work_time"  value="{{ $company['working_hours'] }}" required>
                </div>
                <div class="col-12">
                  <label for="address" class="mt-2 mb-2">Firma manzili</label>
                  <input type="text" name="address" value="{{ $company['address'] }}" class="form-control" required>
                </div>
                <div class="col-6">
                  <label for="service_fee" class="mt-2 mb-2">Xizmat narxi</label>
                  <input type="text" name="service_fee" id="amount" value="{{ $company['service_fee'] }}" class="form-control" required>
                </div>
                <div class="col-6">              
                  <label for="inn" class="mt-2 mb-2">INN</label>
                  <input type="text" name="inn" class="form-control inn" value="{{ $company['inn'] }}" required>
                </div>
                <div class="col-6">
                  <label for="latitude" class="mt-2 mb-2">Latude</label>
                  <input type="text" name="latitude" value="{{ $company['latitude'] }}" required class="form-control long_lat">
                </div>
                <div class="col-6">              
                  <label for="longitude" class="mt-2 mb-2">Longtude</label>
                  <input type="text" name="longitude" value="{{ $company['longitude'] }}" required class="form-control long_lat">
                </div>
                <div class="col-12">              
                  <label for="delivery_radius" class="mt-2 mb-2">Yetqazish radusi (km)</label>
                  <input type="number" name="delivery_radius" value="{{ $company['delivery_radius'] }}" required class="form-control">
                </div>
              </div>
              <label for="description" class="mt-2 mb-2">Firma haqida</label>
              <textarea name="description" required class="form-control">{{ $company['description'] }}</textarea>
              <button class="btn btn-outline-primary w-100 mt-2" type="submit">Malumotlarni yangilash</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Firma balansi</h2>
             <div class="row">
              <div class="col-lg-6">
                <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#create_paymart">Balansini to'ldirish</button>
              </div>
              <div class="col-lg-6">
                <button class="btn btn-outline-primary w-100 mt-lg-0 mt-2" data-bs-toggle="modal" data-bs-target="#firmaBalansTarixi">To'lovlar tarixi</button>
              </div>
             </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Logotipni yangilash</h2>
             <form action="{{ route('companye_update_logo') }}" method="post" enctype="multipart/form-data">
              @csrf 
              <input type="hidden" name="id" value="{{ $company['id'] }}">
              <label for="logo" class="mb-2">Logotip (256x256, jpg)</label>
              <input type="file" name="logo" class="form-control" required>
              <button class="btn btn-outline-primary w-100 mt-2">Logogtipni yangilash</button>
             </form>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Bannerni yangilash</h2>
             <form action="{{ route('companye_update_banner') }}" method="post" enctype="multipart/form-data">
              @csrf 
              <input type="hidden" name="id" value="{{ $company['id'] }}">
              <label for="banner" class="mb-2">Banner (1920x1080px, jpg)</label>
              <input type="file" name="banner" class="form-control" required>
              <button class="btn btn-outline-primary w-100 mt-2">Bannerni yangilash</button>
             </form>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Ish faoliyatini yangilash</h2>
             <form action="{{ route('companye_update_toggle_status') }}" method="post">
              @csrf 
              <input type="hidden" name="id" value="{{ $company['id'] }}">
              @if($company['is_active'] == true)
                <button class="btn btn-outline-danger w-100">Faoliyatini yakunlash</button>
              @else
                <button class="btn btn-outline-success w-100">Faoliyatini qaytadan boshlash</button>
              @endif
             </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Firmaga yangi maxsulot qo'shish</h2>
            <form action="" method="post">
            <input type="hidden" name="company_id" value="{{ $company['id'] }}">
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Firma barcha maxsulotlari</h2>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Fermaga yangi hodim qo'shish</h2>
            <form action="{{ route('companye_create_emploes') }}" method="post">
              @csrf 
              <input type="hidden" name="company_id" value="{{ $company['id'] }}">
              <label for="name" class="mb-2">Firma hodimi</label>
              <input type="text" name="name" class="form-control">
              <label for="phone" class="my-2">Firma hodimi telefon raqami</label>
              <input type="text" name="phone" class="form-control phone" value="+998" required>
              <label for="role" class="my-2">Firma lavozimi</label>
              <select name="role" class="form-control" required>
                <option value="">Tanlang...</option>
                <option value="director">Direktor</option>
                <option value="courier">Xaydavchi</option>
              </select>
              <button class="btn btn-outline-primary mt-4 w-100" type="submit">Saqlash</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">Firma barcha hodimlari</h2>
            <div class="notes-wrapper" style="max-height: 290px; overflow-y: auto; overflow-x: hidden;">
              <div class="table-responsive">
                <table class="table table-bordered" style="font-size: 14px">
                  <thead>
                    <tr class="text-center">
                      <th>#</th>
                      <th>FIO</th>
                      <th>Telefon</th>
                      <th>Lavozimi</th>
                      <th>Status</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($users as $item)
                      <tr>
                        <td class="text-center">{{ $loop->index+1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-center">{{ $item['phone'] }}</td>
                        <td class="text-center">
                          @if($item['role']=='director')
                            Direktor
                          @else
                            Xaydovchi
                          @endif
                        </td>
                        <td class="text-center">
                          @if($item['is_active']==true)                          
                            <span class="badge bg-success">
                              Aktiv
                            </span>
                          @else                          
                            <span class="badge bg-danger">
                              Bloklangan
                            </span>
                          @endif
                        </td>
                        <td>
                          <div class="d-flex justify-content-center align-items-center gap-2">
                            <form action="{{ route('companye_emploes_toggle_status') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                @if($item['is_active'])
                                    <button class="btn btn-sm btn-outline-warning" title="Bloklash"> <i class="bi bi-person-x"></i> </button>
                                @else
                                    <button class="btn btn-sm btn-outline-success" title="Aktivlashtirish"> <i class="bi bi-person-check"></i> </button>
                                @endif
                            </form>
                            <form action="{{ route('companye_emploes_resset_password') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                <button class="btn btn-sm btn-outline-primary" title="Parolni yangilash"> <i class="bi bi-key"></i> </button>
                            </form>
                            <form action="{{ route('companye_emploes_delete') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                <button class="btn btn-sm btn-outline-danger" title="O‘chirish" onclick="return confirm('Rostdan ham o‘chirmoqchimisiz?')"> <i class="bi bi-trash"></i> </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center">Hodimlar mavjud emas.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



<div class="modal" id="create_paymart" tabindex="-1">
  <form action="#" method="post">
    @csrf 
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Filrma Balansini to'ldirish</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          sasa
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
          <button type="submit" class="btn btn-primary">Saqlash</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="firmaBalansTarixi" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Firma balansi tarixi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Non omnis incidunt qui sed occaecati magni asperiores est mollitia. Soluta at et reprehenderit. Placeat autem numquam et fuga numquam. Tempora in facere consequatur sit dolor ipsum. Consequatur nemo amet incidunt est facilis. Dolorem neque recusandae quo sit molestias sint dignissimos.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<style>
  .description-scroll {
      max-height: 72px;   /* taxminan 3 qator */
      overflow-y: auto;
      line-height: 24px;
  }
</style>
@endsection