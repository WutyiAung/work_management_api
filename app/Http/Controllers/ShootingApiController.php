<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShootingRequest;
use App\Models\ShootingAccessory;
use App\Models\ShootingCategory;
use Illuminate\Http\Request;

class ShootingApiController extends Controller
{
    public function create(Request $request){
        $shootingCategory = ShootingCategory::create([
            "name" => $request->input('name')
        ]);
        return response()->json([
            "status" => 200,
            "shootingCategory" => $shootingCategory
        ]);
    }
    public function index(){
       $shootingCategories = ShootingCategory::get();
       return response()->json([
        "status" => 200,
        "shootingCategories" => $shootingCategories
       ]);
    }
    public function update(Request $request, $id)
    {
        // Find the ShootingCategory instance to update
        $shootingCategory = ShootingCategory::findOrFail($id);

        // Update the attributes with the new values
        $shootingCategory->update([
            "name" => $request->input('name')
        ]);

        // Return a JSON response with the updated ShootingCategory
        return response()->json([
            "status" => 200,
            "shootingCategory" => $shootingCategory
        ]);
    }
    public function softDeleteCategoryItems($id)
    {
        // Find all category items with the given category_id
        $subCategories = ShootingCategory::where('id', $id)->get();

        // Update the status of each category item to "soft deleted"
        foreach ($subCategories as $item) {
            $item->status = 'soft_deleted'; // Update the status as per your requirement
            $item->save();
        }

        // Return a response indicating success
        return response()->json([
            "status" => 200,
            "message" => "Category items soft deleted successfully"
        ]);
    }

    //Shooting Accessories
    public function createShootingAccessory(ShootingRequest $request){
        $validatedData = $request->validated();
        $shootingAccessory = ShootingAccessory::create($validatedData);
        return response()->json([
            "status" => 200,
            "shootingAccessory" => $shootingAccessory
        ]);
    }

    public function indexShootingAccessory(){
        $shootingAccessories = ShootingAccessory::with('shootingCategory')->get();
        return response()->json([
            "status" => 200,
            "shootingAccessories" => $shootingAccessories
        ]);
    }

    public function updateShootingAccessory(ShootingRequest $request,$id){
        $shootingAccessory = ShootingAccessory::findOrFail($id);
        $validatedData = $request->validated();
        $shootingAccessory->update($validatedData);
        return response()->json([
            "status" => 200,
            "shootingAccessory" => $shootingAccessory
        ]);
    }
    public function deleteShootingAccessory($id){
        ShootingAccessory::findOrFail($id)->delete();
        return response()->json([
            "status" => 200,
        ]);
    }
}
