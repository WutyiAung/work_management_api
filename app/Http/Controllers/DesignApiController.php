<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\ArtworkSize;
use App\Http\Requests\DesignRequest;
use Illuminate\Support\Facades\File;

class DesignApiController extends Controller
{
    public function create(DesignRequest $request)
    {
        // Validate the request
        $validatedData = $request->validated();
        if($request->hasFile('reference_photo')){
            $photo = $request->file('reference_photo');
            $photoName = uniqid().'_'.$photo->getClientOriginalName();
            $photo->move(public_path().'/file',$photoName);

            // Add the filename to the validated data
            $validatedData['reference_photo'] = $photoName;
        }
        // Create the design
        $design = Design::create($validatedData);

        // Create artwork sizes and associate them with the design
        $artworkSizes = [];
        if ($request->filled('visual_format') && $request->filled('aspect_ratio') && $request->filled('width') && $request->filled('height') && $request->filled('resolution')) {
            $artworkSizeData = [
                'visual_format' => $request->input('visual_format'),
                'aspect_ratio' => $request->input('aspect_ratio'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'resolution' => $request->input('resolution')
            ];

            // Create artwork size
            $artworkSize = ArtworkSize::create($artworkSizeData);

            // Associate artwork size with the design
            $design->artworkSizes()->attach($artworkSize->id);

            // Add artwork size to the list
            $artworkSizes[] = $artworkSize;
        }

        // Return response
        return response()->json([
            "status" => "success",
            "design" => $design,
            "artwork_sizes" => $artworkSizes
        ]);
    }
    public function index()
    {
        // Retrieve all designs along with their associated artwork sizes
        $designs = Design::with('artworkSizes')->get();

        return response()->json([
            "status" => 200,
            "designs" => $designs
        ]);
    }
    public function delete($id){
        $design = Design::findOrFail($id);
        if ($design->reference_photo) {
            File::delete(public_path('file/' . $design->reference_photo));
        }
        $design->delete();
        return response()->json([
            "status" => 200,
        ]);
    }
    public function update(DesignRequest $request, $id)
    {
        // Validate the request
        $validatedData = $request->validated();

        // Find the design to update
        $design = Design::findOrFail($id);

        // Update reference photo if provided
        if ($request->hasFile('reference_photo')) {
            // Delete old reference photo if it exists
            if ($design->reference_photo) {
                File::delete(public_path('file/' . $design->reference_photo));
            }

            // Upload new reference photo
            $photo = $request->file('reference_photo');
            $photoName = uniqid() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('file'), $photoName);

            // Update reference photo in validated data
            $validatedData['reference_photo'] = $photoName;
        } else {
            // No new photo provided, keep the existing one
            $validatedData['reference_photo'] = $design->reference_photo;
        }

        // Update design attributes
        if ($request->filled('visual_format') && $request->filled('aspect_ratio') && $request->filled('width') && $request->filled('height') && $request->filled('resolution')) {
            // If all artwork size fields are provided in the request, update or create artwork size
            $artworkSizeData = [
                'visual_format' => $request->input('visual_format'),
                'aspect_ratio' => $request->input('aspect_ratio'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'resolution' => $request->input('resolution')
            ];

            // Update or create artwork size
            $artworkSize = $design->artworkSizes()->updateOrCreate([], $artworkSizeData);
        } else {
            // If artwork size fields are not provided in the request, keep the existing artwork size
            $artworkSize = $design->artworkSizes()->first();
        }

        // Update design attributes with the validated data
        $design->update($validatedData);

        // Return response
        return response()->json([
            "status" => "success",
            "design" => $design,
            "artworkSize" => $artworkSize
        ]);
    }
    public function designDetail($id){
        $design = Design::with('artworkSizes')->findOrFail($id);
        return response()->json([
            "status" => 200,
            "design" => $design
        ]);
    }
}
