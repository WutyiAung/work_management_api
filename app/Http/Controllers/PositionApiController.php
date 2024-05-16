<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Requests\PositionRequest;

class PositionApiController extends Controller
{
    public function positionCreate(PositionRequest $request){
        $validatedData = $request->validated();
        $position = Position::create($validatedData);
        return response()->json([
            "status" => "success",
            "position" => $position
        ]);
    }
    public function position(){
        $positions = Position::with('department')->get();
        return response()->json([
            "status" => "success",
            "positions" => $positions
        ]);
    }
    public function positionUpdate(PositionRequest $request,$id){
        $position = Position::findOrFail($id);
        $validatedData = $request->validated();
        $position->update($validatedData);
        return response()->json([
            "status" => "success",
            "position" => $position
        ]);
    }
    public function positionDelete($id){
        Position::findOrFail($id)->delete();
        $positions = Position::get();
        return response()->json([
            "status" => "success",
            "positions" => $positions
        ]);
    }
    public function positionDetail($id){
        $position = Position::with('department')->findOrFail($id);
        return response()->json([
            "status" => 200,
            "position" => $position
        ]);
    }
}
