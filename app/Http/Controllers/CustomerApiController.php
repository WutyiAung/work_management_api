<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validatedData = $request->validated();
        $customer = Customer::findOrFail($id);
        $customer->update($validatedData);
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

    // protected function loginProcess(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $validator->errors(),
    //         ], 422);
    //     }

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Invalid credentials.',
    //         ], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'status' => 'success',
    //         'user' => $user,
    //         'token' => $token,
    //     ]);
    // }

    protected function loginProcess(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        // Attempt to authenticate the user
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Generate the token for the authenticated user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the response with the token
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function customerDetails($id){
        $customer = Customer::findOrFail($id);
        return response()->json([
            "status" => 200,
            "customer" => $customer
        ]);
    }
}


