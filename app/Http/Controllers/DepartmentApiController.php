<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest;

class DepartmentApiController extends Controller
{
    public function departmentCreate(DepartmentRequest $request){
        $validation = $request->validated();
        $data = Department::create($validation);
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    public function department(){
        $departments = Department::with('company')->get();
        return response()->json([
            'status' => 'success',
            'departments' => $departments
        ]);
    }
    public function departmentDelete($id){
        Department::findOrFail($id)->delete();
        $departments = Department::get();
        return response()->json([
            'status' => 'success',
            'department' => $departments
        ]);
    }
    public function departmentUpdate(DepartmentRequest $request,$id){
        $validatedData = $request->validated();
        $department = Department::findOrFail($id);
        $department->update($validatedData);
        return response()->json([
            "status" => "success",
            "department" => $department
        ]);
    }
    public function departmentDetail($id){
        $department = Department::with('company')->findOrFail($id);
        return response()->json([
            "status" => 200,
            "department" => $department
        ]);
    }
}
