<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\Validator;

class CustomerApiController extends Controller
{
    //
    public function customerCreate(CustomerRequest $request){
        $validator=$request->validated();
        $data = Customer::create($validator);
        return response()->json([
            "status" => "success",
            "data"=>$data
        ]);
    }
    public function customer(){
        $customers = Customer::get();
        return response()->json([
            "status" => "success",
            "customers"=>$customers
        ]);
    }
    public function customerUpdate(CustomerRequest $request, $id){
        $validatedData = $request->validated(); // Retrieve validated data

        $customer = Customer::findOrFail($id);
        $customer->update($validatedData); // Use the validated data to update

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ]);
    }

    public function customerDelete($id){
        Customer::findOrFail($id)->delete();
        $customers = Customer::get();
        return response()->json([
            'status' => 'success',
            'customers' => $customers
        ]);
    }




    protected function loginProcess(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {


            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!isset($user)) {

            return redirect()->back();
        }
        elseif (!Hash::check($request->password, $user->password)) {

            return redirect()->back();
        }
        return response()->json([
            "status" => "success",
            "user" => $user
        ]);

    }

}


