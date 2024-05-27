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
    public function index(Request $request)
    {
        $state = $request->query('state');
        $query = ShootingCategory::query();
        if ($state === 'soft_deleted') {
            $query->where('status', 'soft_deleted');
        }
        $shootingCategories = $query->get();
        return response()->json([
            'status' => 200,
            'shootingCategories' => $shootingCategories,
        ]);
    }
    public function update(Request $request, $id)
    {
        $shootingCategory = ShootingCategory::findOrFail($id);
        $shootingCategory->update([
            "name" => $request->input('name')
        ]);
        return response()->json([
            "status" => 200,
            "shootingCategory" => $shootingCategory
        ]);
    }
    public function softDeleteCategoryItems($id)
    {
        $subCategories = ShootingCategory::where('id', $id)->get();
        foreach ($subCategories as $item) {
            $item->status = 'soft_deleted';
            $item->save();
        }
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
    public function getShootingAccessory($id){
        $shootingAccessories = ShootingAccessory::where('shooting_category_id',$id)->with('shootingCategory')->get();
        return response()->json([
            "status" => 200,
            "shootingAccessory" => $shootingAccessories
        ]);
    }
    public function shootingCategoryDetail($id){
        $shootingCategory = ShootingCategory::findOrFail($id);
        return response()->json([
            "status" => 200,
            "shootingCategory" => $shootingCategory
        ]);
    }
    public function shootingAccessoryDetail($id){
        $shootingAccessory = ShootingAccessory::with('shootingCategory')->findOrFail($id);
        return response()->json([
            "status" => "200",
            "shootingAccessory" => $shootingAccessory
        ]);
    }
    public function updateDetail(Request $request,$id){
        $shootingCategory = ShootingCategory::findOrFail($id);
        $shootingCategory->name = $request->name;
        $shootingCategory->save();
        return response()->json([
            "status" => 200,
            "shootingCategory" => $shootingCategory
        ]);
    }
    public function updateShootingAccessoryDetail(Request $request,$id){
        $shootingAccessory = ShootingAccessory::findOrFail($id);
        $shootingAccessory->name = $request->name;
        $shootingAccessory->shooting_category_id = $request->shooting_category_id;
        $shootingAccessory->save();
        return response()->json([
            "status" => 200,
            "shootingAccessory" => $shootingAccessory
        ]);
    }
    public function getShootingAccessoryDetail($id){
        $shootingAccessory = ShootingAccessory::findOrFail($id);
        return response()->json([
            "status" => 200,
            "shootingAccessory" => $shootingAccessory
        ]);
    }
    public function softDeleteCategoriesWithQuery(Request $request)
    {
        // Get the 'state' query parameter from the request
        $state = $request->query('state');

        // Filter the categories where status is 'soft_deleted' if the state is 'soft_deleted'
        if ($state === 'soft_deleted') {
            $softDeletedCategories = ShootingCategory::where('status', 'soft_deleted')->get();

            // Return the filtered categories as a JSON response
            return response()->json([
                'status' => 200,
                'softDeletedCategories' => $softDeletedCategories,
            ]);
        }

        // If the state is not 'soft_deleted', return an empty array or appropriate response
        return response()->json([
            'status' => 400,
            'message' => 'Invalid state parameter',
        ]);
    }
}
