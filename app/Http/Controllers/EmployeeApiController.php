<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EmployeeRequest;

class EmployeeApiController extends Controller
{
    public function employeeCreate(EmployeeRequest $request){
         $validatedData = $request->validated();
         $validatedData['password'] = Hash::make($validatedData['password']);
        if($request->hasFile('photo_path')){
            $mainPhoto = $request->file('photo_path');
            $mainPhotoName = uniqid().'_'.$mainPhoto->getClientOriginalName();
            $mainPhoto->move(public_path().'/file',$mainPhotoName);
             $validatedData['photo_path'] = $mainPhotoName;
        } else {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
        $employee = User::create($validatedData);
        return response()->json([
            'status' => 'success',
            'employee' => $employee
        ]);
    }
    public function employee(Request $request) {
        $companyId = $request->query('company_id');
        $query = User::with('company', 'department', 'position');
        if ($companyId && $companyId !== 'undefined' && $companyId !== 'null') {
            $query->where('company_id', $companyId);
        }
        $employees = $query->get();
        return response()->json([
            'status' => 'success',
            'employees' => $employees
        ]);
    }

    public function EmployeeDelete($id){
        $employee = User::findOrFail($id);
        if ($employee->photo_path) {
            File::delete(public_path('file/' . $employee->photo_path));
        }
        $employee->delete();
        $Employees = User::get();
        return response()->json([
            'status' => 'success',
            'Employee' => $Employees
        ]);
    }
    public function employeeUpdate(Request $request, $id)
    {
        // Validate the request data except for the password field
        $validatedData = $request->validate([
            'name' => 'required',
            'company_id' => 'required',
            'position_id' => 'required',
            'email' => 'required',
            'password' => 'nullable',
            'phone' => 'required',
            'gender' => 'required',
            'nrc_number' => 'required',
            'department_id' => 'required',
            'photo_path' => 'nullable',
        ]);
        $employee = User::findOrFail($id);
        // Update the email if it has changed
        if ($request->has('email') && $validatedData['email'] != $employee->email) {
            $employee->email = $validatedData['email'];
        }
        // Update the photo path if a new file is uploaded
        if ($request->hasFile('photo_path')) {
            $contractPath = $request->file('photo_path');
            $contractPathName = uniqid() . '_' . $contractPath->getClientOriginalName();
            $contractPath->move(public_path('/file'), $contractPathName);

            // Delete the old file if it exists
            if ($employee->photo_path) {
                File::delete(public_path('/file/' . $employee->photo_path));
            }

            // Update the attachment path in the database
            $validatedData['photo_path'] = $contractPathName;
        } else {
            // Retain the existing file path if no new file is uploaded
            $validatedData['photo_path'] = $employee->photo_path;
        }
        // Hash the password if it is provided
        if ($request->password) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            // Ensure the password is not overwritten with null
            unset($validatedData['password']);
        }

        // Update the employee data
        $employee->fill($validatedData);
        $employee->save();
        return response()->json([
            "status" => "success",
            "employee" => $employee
        ]);
    }
    public function employeeDetail($id){
        $employee = User::with('company','department','position')->findOrFail($id);
        return response()->json([
            "status" => 200,
            "employee" => $employee
        ]);
    }
}
