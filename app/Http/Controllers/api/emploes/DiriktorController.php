<?php

namespace App\Http\Controllers\api\emploes;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\emploes\company\CreateEmploesRequest;
use App\Http\Requests\api\emploes\company\UpdateCompanyStatusRequest;
use App\Http\Requests\api\emploes\company\UpdateEmployeeStatusRequest;
use App\Http\Requests\api\emploes\company\UpdateProductStatusRequest;
use App\Models\Company;
use App\Models\CompanyBalanceTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;

class DiriktorController extends Controller{

    public function setting(): JsonResponse{
        $user = auth()->user();
        $companyId = $user->company_id;
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => "Kompaniyangiz haqida ma'lumot mavjud emas"
            ], 404);
        }
        $products = Product::where('company_id', $companyId)->select('id', 'name', 'description', 'price', 'image', 'image_banner', 'is_active')->latest()->get();
        $employees = User::where('company_id', $companyId)->select('id', 'name', 'phone', 'role', 'is_active')->get();
        $paymentHistory = CompanyBalanceTransaction::where('company_id', $companyId)->select('id', 'type', 'amount', 'balance_joriy', 'balance_kiyingi', 'description', 'created_at')->latest()->take(50)->get();
        return response()->json([
            'success' => true,
            'data' => [
                'company' => [
                    'id' => $company->id,
                    'company_name' => $company->company_name,
                    'direktor' => $company->direktor,
                    'phone' => $company->phone,
                    'address' => $company->address,
                    'balance' => $company->balance,
                    'service_fee' => $company->service_fee,
                    'rating' => $company->rating . " (" . $company->rating_count . ")",
                    'description' => $company->description,
                    'logo' => $company->logo,
                    'banner' => $company->banner,
                    'working_hours' => $company->working_hours,
                    'latitude' => $company->latitude,
                    'longitude' => $company->longitude,
                    'delivery_radius' => $company->delivery_radius,
                    'product_count' => $products->count(),
                    'emploes_count' => $employees->count(),
                ],
                'products' => $products,
                'employees' => $employees,
                'payment_history' => $paymentHistory,
            ]
        ]);
    }

    public function setting_create_emploes(CreateEmploesRequest $request){
        $director = auth()->user();
        $employee = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'company_id' => $director->company_id,
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Yangi xodim muvaffaqiyatli qo‘shildi',
            'data' => $employee
        ], 200);
    }

    public function updateStatus(UpdateEmployeeStatusRequest $request){
        $director = auth()->user();
        $employee = User::where('id', $request->employee_id)->where('company_id', $director->company_id)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Xodim topilmadi yoki sizda uni tahrirlash huquqi yo‘q.'
            ], 403);
        }
        $employee->is_active = $request->is_active;
        $employee->save();
        if (!$employee->is_active) {
            $employee->tokens()->delete();
        }
        return response()->json([
            'success' => true,
            'message' => "Xodim statusi muvaffaqiyatli yangilandi.",
            'data' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'is_active' => $employee->is_active
            ]
        ]);
    }

    public function updateCompanyStatus(UpdateCompanyStatusRequest $request){
        $director = auth()->user();
        $company = Company::where('id', $director->company_id)->first();
        $company->is_active = $request->is_active;
        $company->save();
        return response()->json([
            'success' => true,
            'message' => "Komaniya statusi muvaffaqiyatli yangilandi.",
        ]);
    }

    public function updateProductStatus(UpdateProductStatusRequest $request){
        $director = auth()->user();
        $product = Product::where('id', $request->product_id)->where('company_id', $director->company_id)->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Maxsulot topilmadi yoki sizda uni tahrirlash huquqi yo‘q.'
            ], 403);
        }
        $product->is_active = $request->is_active;
        $product->save();
        return response()->json([
            'success' => true,
            'message' => "Maxsulot statusi muvaffaqiyatli yangilandi.",
        ]);
    }

}
