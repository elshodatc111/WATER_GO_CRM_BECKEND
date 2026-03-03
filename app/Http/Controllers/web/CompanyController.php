<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\web\company\StoreCompanyRequest;
use App\Http\Requests\Web\Company\ToggleCompanyStatusRequest;
use App\Http\Requests\Web\Company\UpdateCompanyBannerRequest;
use App\Http\Requests\Web\Company\UpdateCompanyLogoRequest;
use App\Http\Requests\Web\Company\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CompanyController extends Controller{

    public function companyee(){
        $companyee = Company::select('id','company_name','direktor','balance','service_fee','rating','rating_count','is_active')->get();
        return view('company.index',compact('companyee'));
    }

    public function store(StoreCompanyRequest $request){
        $logoDir = public_path('companies/logos');
        $bannerDir = public_path('companies/banners');
        if (!file_exists($logoDir)) { mkdir($logoDir, 0755, true); }
        if (!file_exists($bannerDir)) { mkdir($bannerDir, 0755, true); }
        $logoFile = $request->file('logo');
        $logoName = 'logo_' . Str::uuid() . '.' . $logoFile->getClientOriginalExtension();
        $logoFile->move($logoDir, $logoName);
        $bannerFile = $request->file('banner');
        $bannerName = 'banner_' . Str::uuid() . '.' . $bannerFile->getClientOriginalExtension();
        $bannerFile->move($bannerDir, $bannerName);
        Company::create([
            'company_name' => $request->company_name,
            'direktor' => $request->direktor,
            'phone' => $request->phone,
            'address' => $request->address,
            'working_hours' => $request->working_hours,
            'service_fee' => $request->service_fee,
            'inn' => $request->inn,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'delivery_radius' => $request->delivery_radius,
            'description' => $request->description,
            'logo' => 'companies/logos/' . $logoName,
            'banner' => 'companies/banners/' . $bannerName,
        ]);
        return back()->with('success', 'Firma muvaffaqiyatli qo‘shildi');
    }

    public function show($id){
        $company = Company::findOrFail($id);
        return view('company.show',compact('company'));
    }

    public function update(UpdateCompanyRequest $request){
        $company = Company::findOrFail($request->id);
        $company->update([
            'company_name' => $request->company_name,
            'direktor' => $request->direktor,
            'phone' => $request->phone,
            'address' => $request->address,
            'working_hours' => $request->working_hours,
            'service_fee' => $request->service_fee,
            'inn' => $request->inn,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'delivery_radius' => $request->delivery_radius,
            'description' => $request->description,
        ]);
        return back()->with('success', 'Maʼlumotlar muvaffaqiyatli yangilandi');
    }

    public function updateLogo(UpdateCompanyLogoRequest $request){
        $company = Company::findOrFail($request->id);
        if ($company->logo && file_exists(public_path($company->logo))) {
            unlink(public_path($company->logo));
        }
        $file = $request->file('logo');
        $fileName = 'logo_' . Str::uuid() . '.jpg';
        $file->move(public_path('companies/logos'), $fileName);
        $company->update([
            'logo' => 'companies/logos/' . $fileName,
        ]);
        return back()->with('success', 'Logotip muvaffaqiyatli yangilandi');
    }

    public function updateBanner(UpdateCompanyBannerRequest $request){
        $company = Company::findOrFail($request->id);
        if ($company->banner && file_exists(public_path($company->banner))) {
            unlink(public_path($company->banner));
        }
        $file = $request->file('banner');
        $fileName = 'banner_' . Str::uuid() . '.jpg';
        $file->move(public_path('companies/banners'), $fileName);
        $company->update([
            'banner' => 'companies/banners/' . $fileName,
        ]);
        return back()->with('success', 'Banner muvaffaqiyatli yangilandi');
    }

    public function toggleStatus(ToggleCompanyStatusRequest $request){
        $company = Company::findOrFail($request->id);
        $company->update([
            'is_active' => !$company->is_active
        ]);
        return back()->with('success', $company->is_active ? 'Firma faoliyati qayta tiklandi' : 'Firma faoliyati tugatildi' );
    }

}
