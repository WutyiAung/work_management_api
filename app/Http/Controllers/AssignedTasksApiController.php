<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\ArtworkSize;
use App\Models\AssignedTask;
use Illuminate\Http\Request;
use App\Http\Requests\AssignedTasksRequest;

class AssignedTasksApiController extends Controller
{
    public function assignedTasksCreate(AssignedTasksRequest $request) {
        try {
            // Validate incoming request data
            $validatedData = $request->validated();
            // Set default status to 'pending' if not provided
            $validatedData['status'] = $validatedData['status'] ?? 'pending';

            // Create AssignedTask
            $assignedTasks = AssignedTask::create($validatedData);

            // Handle Designs
            $designs = [];
            if ($request->filled('brand')) {
                // Handle reference photo
                $photoName = null; // Initialize photo name variable
                if ($request->hasFile('reference_photo')) {
                    $photo = $request->file('reference_photo');
                    $photoName = uniqid() . '_' . $photo->getClientOriginalName();
                    $photo->move(public_path('file'), $photoName);
                }

                // Create Design
                $design = Design::create($request->only([
                    'brand', 'type_of_media', 'deadline', 'content_writer_id', 'designer_id',
                    'visual_copy', 'headline', 'body', 'objective', 'important_info',
                    'taste_style', 'reference','reference_photo' => $photoName
                ]));

                // Add reference_photo to design data
                $designData['reference_photo'] = $photoName;

                $design = Design::create($designData);
                // Attach design to assigned task
                $assignedTasks->design()->attach($design->id);
                $designs[] = $design;

                // Handle Artwork Sizes
                if ($request->filled('visual_format') && $request->filled('aspect_ratio') &&
                    $request->filled('width') && $request->filled('height') && $request->filled('resolution')) {
                    $artworkSize = ArtworkSize::create($request->only([
                        'visual_format', 'aspect_ratio', 'width', 'height', 'resolution'
                    ]));
                    // Attach artwork size to design
                    $design->artworkSizes()->attach($artworkSize->id);
                }
            }

            // Return success response
            return response()->json([
                "status" => "success",
                "assignedTasks" => $assignedTasks,
                "designs" => $designs,
                "artwork_size" => $artworkSize
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                "status" => "error",
                "message" => "An error occurred while processing the request.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    //GET
    public function assignedTasks(){
        $assignedTasks = AssignedTask::with('customer','project','user','design.artworkSizes')->get();
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
