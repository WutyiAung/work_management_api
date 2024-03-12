<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignedTasksRequest;
use App\Models\AssignedTask;
use Illuminate\Http\Request;

class AssignedTasksApiController extends Controller
{
    //Assigned Tasks
    public function assignedTasksCreate(AssignedTasksRequest $request){
        $validatedData = $request->validated();
        $validatedData['status'] = $validatedData['status'] ?? 'pending';
        $assignedTasks = AssignedTask::create($validatedData);
        return response()->json([
            "status" => "success",
            "assignedTasks" => $assignedTasks
        ]);
    }
    //GET
    public function assignedTasks(){
        $assignedTasks = AssignedTask::with('customer','project','employee')->get();
        return response()->json([
            "status" => "success",
            "assignedTasks" => $assignedTasks
        ]);
    }
    //DELETE
    public function assignedTasksDelete($id){
        AssignedTask::findOrFail($id)->delete();
        $assignedTasks = AssignedTask::get();
        return response()->json([
            'status' => 'success',
            'Employee' => $assignedTasks
        ]);
    }
    //UPDATE
    public function assignedTasksUpdate(AssignedTasksRequest $request, $id)
    {
        $validatedData = $request->validated();
        // Check if 'status' is not provided or its value is null, then set default value
        if (!isset($validatedData['status']) || $validatedData['status'] === null) {
            $validatedData['status'] = 'pending';
        }
        $assignedTask = AssignedTask::findOrFail($id);
        $assignedTask->update($validatedData);
        return response()->json([
            "status" => "success",
            "assignedTask" => $assignedTask
        ]);
    }
}
