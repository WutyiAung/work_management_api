<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyApiController extends Controller
{
    public function companyCreate(CompanyRequest $request){
        $validator = $request->validated();
        $data = Company::create($validator);
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    public function company(){
        $companies = Company::with('company')->get();
        return response()->json([
            'status' => 'success',
            'companies' => $companies
        ]);
    }
    public function companyUpdate(CompanyRequest $request,$id){
        $company = Company::findOrFail($id);
        $validatedData = $request->validated();
        $company->update($validatedData);
        return response()->json([
            "status" => "success",
            "company" => $company
        ]);
    }
    public function companyDelete($id){
        Company::findOrFail($id)->delete();
        $companies = Company::get();
        return response()->json([
            "status" => "success",
            "companies" => $companies
        ]);
    }
}
