<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CompanyController extends Controller{

    #[OA\Get(
        path: "/v1/user/companies",
        summary: "Barcha aktiv suv firmalar ro‘yxati",
        description: "Mijoz ilovasi uchun barcha aktiv firmalar (kompaniyalar) ro‘yxatini qaytaradi. Reyting bo‘yicha saralangan.",
        tags: ["Mijoz – Kompaniyalar"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Firmalar ro‘yxati muvaffaqiyatli qaytarildi"
            )
        ]
    )]
    public function allCompany(){
        $companies = Company::where('is_active', true)->orderBy('rating', 'desc')->orderBy('rating_count', 'desc')->get();
        return CompanyResource::collection($companies);
    }

    #[OA\Get(
        path: "/v1/user/companiee/{id}",
        summary: "Bitta firma haqida batafsil maʼlumot",
        description: "Berilgan ID bo‘yicha firmaga tegishli asosiy maʼlumotlar va faqat aktiv mahsulotlar ro‘yxatini qaytaradi.",
        tags: ["Mijoz – Kompaniyalar"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Firma ID si",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Firma topildi va maʼlumotlar qaytarildi"
            ),
            new OA\Response(
                response: 404,
                description: "Firma topilmadi yoki aktiv emas"
            )
        ]
    )]
    public function companyShow($id){
        $company = Company::active()->with(['products' => function($query) {$query->where('is_active', true);}])->findOrFail($id);
        return new CompanyResource($company);
    }

}
