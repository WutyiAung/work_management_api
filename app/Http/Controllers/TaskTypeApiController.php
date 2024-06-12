<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Illuminate\Http\Request;

class TaskTypeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taskTypes = TaskType::get();
        return response()->json([
            'status' => 201,
            'taskTypes' => $taskTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required',
            'table_name' => 'required|string|max:255',
            'table_type' => 'required',
        ]);
        $taskType = TaskType::create($validatedData);
        return response()->json([
            'status' => 201,
            'taskType' => $taskType,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskType $taskType)
    {
        return response()->json([
            'status' => 200,
            'taskType' => $taskType
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskType $taskType)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required',
            'table_name' => 'required|string|max:255',
            'table_type' => 'required',
        ]);
        $taskType->update($validatedData);
        return response()->json([
            'status' => 200,
            'task_type' => $taskType
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskType $taskType)
    {
        $taskType->delete();
        return response()->json([
            'status' => 200,
        ]);
    }
}
