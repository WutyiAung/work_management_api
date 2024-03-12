<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectTypeRequest;
use App\Models\TaskType;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\TaskTypeRequest;
use App\Models\ProjectType;

class TaskProjectTypeApiController extends Controller
{
    public function create(TaskTypeRequest $request){
        $validatedData = $request->validated();
        $taskType = TaskType::create($validatedData);
        return response()->json([
            'status' => 200,
            'taskType' => $taskType
        ]);
    }
    public function index(){
        $taskTypes = TaskType::all();
        return response()->json([
            "status" => 200,
            "taskTypes" => $taskTypes
        ]);
    }
    public function update(TaskTypeRequest $request,$id){
        $taskType = TaskType::findOrFail($id);
        $validatedData = $request->validated();
        $taskType->update($validatedData);
        return response()->json([
            "status" => 200,
            "taskType" => $taskType
        ]);
    }
    public function delete($id){
        TaskType::findOrFail($id)->delete();
        return response()->json([
            "status" => 200,
        ]);
    }

    public function createProjectType(ProjectTypeRequest $request){
        $validatedData = $request->validated();
        $projectType = ProjectType::create($validatedData);
        return response()->json([
            "status" => 200,
            "projectType" => $projectType
        ]);
    }
    public function indexProjectType(){
        $projectTypes = ProjectType::with('taskType')->get();
        return response()->json([
            "status" => 200,
            "projectTypes" => $projectTypes
        ]);
    }
    public function updateProjectType(ProjectTypeRequest $request,$id){
        $validatedData = $request->validated();
        $projectType = ProjectType::findOrFail($id);
        $projectType->update($validatedData);
        return response()->json([
            "status" => 200,
            "projectType" => $projectType
        ]);
    }
    public function deleteProjectType($id){
        ProjectType::findOrFail($id)->delete();
        return response()->json([
            "status" => 200,
        ]);
    }
}
