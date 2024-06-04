<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Shooting;
use App\Models\ArtworkSize;
use App\Models\AssignedTask;
use Illuminate\Support\Facades\Log;
use App\Models\ShootingAccessoryCategory;
use App\Http\Requests\AssignedTasksRequest;

class AssignedTasksApiController extends Controller
{
    public function assignedTasksCreate(AssignedTasksRequest $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validated();
            // Set default status to 'pending' if not provided
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            // Create AssignedTask
            $assignedTasks = AssignedTask::create($validatedData);
            if ($request->filled('brand')) {
                // Handle Designs
                $designs = [];
                // Handle reference photo
                // Create Design
                $designData = $request->only([
                    'brand', 'type_of_media', 'deadline', 'content_writer_id', 'designer_id',
                    'visual_copy', 'headline', 'body', 'objective', 'important_info',
                    'taste_style', 'reference'
                ]);
                // Handle reference photo
                $photoName = null; // Initialize photo name variable
                if ($request->hasFile('reference_photo')) {
                    $photo = $request->file('reference_photo');
                    $photoName = uniqid() . '_' . $photo->getClientOriginalName();
                    $photo->move(public_path('file'), $photoName);
                    $designData['reference_photo'] = $photoName;
                }
                $design = Design::create($designData);
                // Attach design to assigned task
                $assignedTasks->design()->attach($design->id);
                $designs[] = $design;
                // Handle Artwork Sizes
                if (
                    $request->filled('visual_format') && $request->filled('aspect_ratio') &&
                    $request->filled('width') && $request->filled('height') && $request->filled('resolution')
                ) {
                    $artworkSize = ArtworkSize::create($request->only([
                        'visual_format', 'aspect_ratio', 'width', 'height', 'resolution'
                    ]));
                    // Attach artwork size to design
                    $design->artworkSizes()->attach($artworkSize->id);
                }
                // Return success response
                return response()->json([
                    "status" => "success",
                    "assignedTasks" => $assignedTasks,
                    "designs" => $designs,
                    "artwork_size" => $artworkSize
                ]);
            } else if ($request->filled('shooting_location')) {
                // Handle Designs
                $shootings = [];
                // Handle reference photo
                // Create Design
                $shootingData = $request->only([
                    'shooting_location', 'type_detail', 'script_detail', 'scene_number', 'contact_name', 'contact_phone', 'duration', 'type', 'client', 'date', 'time', 'video_shooting_project', 'photo_shooting_project', 'arrive_office_on_time', 'transportation_charge', 'out_time', 'in_time', 'crew_list', 'project_details'
                ]);
                // Handle reference photo
                $fileName = null; // Initialize photo name variable
                if ($request->hasFile('document')) {
                    $file = $request->file('document');
                    $fileName = uniqid() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('file'), $fileName);
                    $shootingData['document'] = $fileName;
                }
                $shooting = Shooting::create($shootingData);
                // Attach shooting to assigned task
                $assignedTasks->shooting()->attach($shooting->id);
                $shootings[] = $shooting;
                Log::info($request->all());
                // Handle Artwork Sizes
                if ($request->filled('shooting_accessories')) {
                    $shootingCategories = json_decode($request->input('shooting_accessories'), true);
                    Log::info('Decoded shooting categories: ' . print_r($shootingCategories, true));
                    // Ensure shooting_categories is an array
                    if (is_array($shootingCategories)) {
                        foreach ($shootingCategories as $category) {
                            // Create ShootingAccessoryCategory for each category
                            $shootingCategory = ShootingAccessoryCategory::create([
                                'accessory_name' => $category['accessory_name'],
                                'required_qty' => $category['required_qty'],
                                'taken_qty' => $category['required_qty'],
                                'returned_qty' => $category['returned_qty']
                            ]);
                            // Attach the shooting category to the shooting
                            $shooting->shootingAccessoryCategories()->attach($shootingCategory->id);
                        }
                        // Return success response after processing all categories
                        return response()->json(['message' => 'Data stored successfully'], 200);
                    } else {
                        // Handle error if shooting_categories is not an array
                        return response()->json(['error' => 'The shooting accessories field must be an array.'], 400);
                    }
                }
                // Return success response
                return response()->json([
                    "status" => "success",
                    "assignedTasks" => $assignedTasks,
                ]);
            }
            // Return success response
            return response()->json([
                "status" => "success",
                "assignedTasks" => $assignedTasks,
            ]);
            // Return success response
            return response()->json([
                "status" => "success",
                "assignedTasks" => $assignedTasks,
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
    public function assignedTasks()
    {
        $assignedTasks = AssignedTask::with('customer', 'project', 'user', 'design.artworkSizes')->get();
        return response()->json([
            "status" => "success",
            "assignedTasks" => $assignedTasks
        ]);
    }
    //DELETE
    public function assignedTasksDelete($id)
    {
        $assignedTask = AssignedTask::findOrFail($id);
        $assignedTask->delete();
        $assignedTasks = AssignedTask::all();
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
    public function assignedTasksDetails($id)
    {
        // Fetch the assigned task with related data
        $assignedTask = AssignedTask::where('id', $id)
            ->with('customer', 'project', 'user','shooting.shootingAccessoryCategories')
            ->first();

        // Initialize response array
        $response = [
            'status' => 'success',
            'assignedTask' => $assignedTask->makeHidden(['design', 'shooting'])
        ];

        // Add first design and its artwork sizes if available
        if ($assignedTask && isset($assignedTask->design[0])) {
            $response['assignedTask']['designData'] = $assignedTask->design[0];
            $response['assignedTask']['artworkSizes'] = $assignedTask->design[0]->artwork_sizes;
        } else {
            $response['assignedTask']['designData'] = null;
            $response['assignedTask']['artworkSizes'] = null;
        }

        // Add first shooting and its accessory categories if available
        if ($assignedTask && isset($assignedTask->shooting[0])) {
            $response['assignedTask']['shootingData'] = $assignedTask->shooting[0];
        } else {
            $response['assignedTask']['shootingData'] = null;
        }
        return response()->json($response);
    }




    public function assignedTasksEmployee($id)
    {
        $assignedTasks = AssignedTask::where('user_id', $id)
            ->with(['customer', 'project', 'user', 'design.artworkSizes', 'shooting.shootingAccessoryCategories'])
            ->get();
        // Filter out empty 'design' and 'shooting' relationships
        $assignedTasks = $assignedTasks->map(function ($task) {
            if ($task->relationLoaded('design') && $task->design->isEmpty()) {
                unset($task->design);
            }
            if ($task->relationLoaded('shooting') && $task->shooting->isEmpty()) {
                unset($task->shooting);
            }
            return $task;
        });
        return response()->json([
            "status" => 200,
            "assignedTasks" => $assignedTasks
        ]);
    }
}
