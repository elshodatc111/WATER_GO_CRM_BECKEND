<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller{

    public function allCompany(){
        $companies = Company::where('is_active', true)->orderBy('rating', 'desc')->orderBy('rating_count', 'desc')->get();
        return CompanyResource::collection($companies);
    }

    public function companyShow($id){
        $company = Company::active()->with(['products' => function($query) {$query->where('is_active', true);}])->findOrFail($id);
        return new CompanyResource($company);
    }

}
