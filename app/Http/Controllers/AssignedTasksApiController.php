<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Shooting;
use App\Models\ArtworkSize;
use App\Models\AssignedTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ShootingAccessoryCategory;
use App\Http\Requests\AssignedTasksRequest;

class AssignedTasksApiController extends Controller
{
    // public function assignedTasksCreate(AssignedTasksRequest $request)
    // {
    //     try {
    //         $validatedData = $request->validated();
    //         $validatedData['status'] = $validatedData['status'] ?? 'pending';
    //         $assignedTasks = AssignedTask::create($validatedData);
    //         if ($request->filled('brand')) {
    //             $designs = [];
    //             $designData = $request->only([
    //                 'brand', 'type_of_media', 'deadline', 'content_writer_id', 'designer_id',
    //                 'visual_copy', 'headline', 'body', 'objective', 'important_info',
    //                 'taste_style', 'reference'
    //             ]);
    //             $photoName = null;
    //             if ($request->hasFile('reference_photo')) {
    //                 $photo = $request->file('reference_photo');
    //                 $photoName = uniqid() . '_' . $photo->getClientOriginalName();
    //                 $photo->move(public_path('file'), $photoName);
    //                 $designData['reference_photo'] = $photoName;
    //             }
    //             $design = Design::create($designData);
    //             $assignedTasks->design()->attach($design->id);
    //             $designs[] = $design;
    //             Log::info($request);
    //             if (
    //                 $request->filled('visual_format') && $request->filled('aspect_ratio') &&
    //                 $request->filled('width') && $request->filled('height') && $request->filled('resolution')
    //             ) {
    //                 $artworkSize = ArtworkSize::create($request->only([
    //                     'visual_format', 'aspect_ratio', 'width', 'height', 'resolution'
    //                 ]));
    //                 Log::info($request);
    //                 $design->artworkSizes()->attach($artworkSize->id);
    //             }
    //             return response()->json([
    //                 "status" => "success",
    //                 "assignedTasks" => $assignedTasks,
    //                 "designs" => $designs,
    //                 "artwork_size" => $artworkSize
    //             ]);
    //         } else if ($request->filled('shooting_location')) {
    //             $shootings = [];
    //             $shootingData = $request->only([
    //                 'shooting_location', 'type_detail', 'script_detail', 'scene_number', 'contact_name', 'contact_phone', 'duration', 'type', 'client', 'date', 'time', 'video_shooting_project', 'photo_shooting_project', 'arrive_office_on_time', 'transportation_charge', 'out_time', 'in_time', 'crew_list', 'project_details'
    //             ]);
    //             $fileName = null;
    //             if ($request->hasFile('document')) {
    //                 $file = $request->file('document');
    //                 $fileName = uniqid() . '_' . $file->getClientOriginalName();
    //                 $file->move(public_path('file'), $fileName);
    //                 $shootingData['document'] = $fileName;
    //             }
    //             $shooting = Shooting::create($shootingData);
    //             $assignedTasks->shooting()->attach($shooting->id);
    //             $shootings[] = $shooting;
    //             Log::info($request->all());
    //             if ($request->filled('shooting_accessories')) {
    //                 $shootingCategories = json_decode($request->input('shooting_accessories'), true);
    //                 Log::info('Decoded shooting categories: ' . print_r($shootingCategories, true));
    //                 if (is_array($shootingCategories)) {
    //                     foreach ($shootingCategories as $category) {
    //                         $shootingCategory = ShootingAccessoryCategory::create([
    //                             'accessory_name' => $category['accessory_name'],
    //                             'required_qty' => $category['required_qty'],
    //                             'taken_qty' => $category['required_qty'],
    //                             'returned_qty' => $category['returned_qty']
    //                         ]);
    //                         $shooting->shootingAccessoryCategories()->attach($shootingCategory->id);
    //                     }
    //                     return response()->json(['message' => 'Data stored successfully'], 200);
    //                 } else {
    //                     return response()->json(['error' => 'The shooting accessories field must be an array.'], 400);
    //                 }
    //             }
    //             return response()->json([
    //                 "status" => "success",
    //                 "assignedTasks" => $assignedTasks,
    //             ]);
    //         }
    //         return response()->json([
    //             "status" => "success",
    //             "assignedTasks" => $assignedTasks,
    //         ]);
    //         return response()->json([
    //             "status" => "success",
    //             "assignedTasks" => $assignedTasks,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "status" => "error",
    //             "message" => "An error occurred while processing the request.",
    //             "error" => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function assignedTasksCreate(AssignedTasksRequest $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            $assignedTasks = AssignedTask::create($validatedData);

            if ($request->filled('brand')) {
                $design = $this->handleDesign($request);
                $assignedTasks->design()->attach($design->id);
            } elseif ($request->filled('shooting_location')) {
                $shooting = $this->handleShooting($request);
                $assignedTasks->shooting()->attach($shooting->id);
            }

            DB::commit();

            return response()->json([
                "status" => "success",
                "assignedTasks" => $assignedTasks,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating assigned task: ' . $e->getMessage());

            return response()->json([
                "status" => "error",
                "message" => "An error occurred while processing the request.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    private function handleDesign($request)
    {
        $designData = $request->only([
            'brand', 'type_of_media', 'deadline', 'content_writer_id', 'designer_id',
            'visual_copy', 'headline', 'body', 'objective', 'important_info',
            'taste_style', 'reference'
        ]);

        if ($request->hasFile('reference_photo')) {
            $photo = $request->file('reference_photo');
            $photoName = uniqid() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('file'), $photoName);
            $designData['reference_photo'] = $photoName;
        }

        $design = Design::create($designData);

        if (
            $request->filled('visual_format') && $request->filled('aspect_ratio') &&
            $request->filled('width') && $request->filled('height') && $request->filled('resolution')
        ) {
            $artworkSize = ArtworkSize::create($request->only([
                'visual_format', 'aspect_ratio', 'width', 'height', 'resolution'
            ]));
            $design->artworkSizes()->attach($artworkSize->id);
        }

        return $design;
    }

    private function handleShooting($request)
    {
        $shootingData = $request->only([
            'shooting_location', 'type_detail', 'script_detail', 'scene_number', 'contact_name',
            'contact_phone', 'duration', 'type', 'client', 'date', 'time', 'video_shooting_project',
            'photo_shooting_project', 'arrive_office_on_time', 'transportation_charge', 'out_time',
            'in_time', 'crew_list', 'project_details'
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('file'), $fileName);
            $shootingData['document'] = $fileName;
        }

        $shooting = Shooting::create($shootingData);

        if ($request->filled('shooting_accessories')) {
            $shootingCategories = json_decode($request->input('shooting_accessories'), true);
            if (is_array($shootingCategories)) {
                foreach ($shootingCategories as $category) {
                    $shootingCategory = ShootingAccessoryCategory::create([
                        'accessory_name' => $category['accessory_name'],
                        'required_qty' => $category['required_qty'],
                        'taken_qty' => $category['taken_qty'],
                        'returned_qty' => $category['returned_qty']
                    ]);
                    $shooting->shootingAccessoryCategories()->attach($shootingCategory->id);
                }
            } else {
                throw new \Exception('The shooting accessories field must be an array.');
            }
        }

        return $shooting;
    }

    public function assignedTasksUpdate(AssignedTasksRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $assignedTask = AssignedTask::findOrFail($id);
            $validatedData = $request->validated();
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            $assignedTask->update($validatedData);

            if ($request->filled('brand')) {
                $this->updateDesign($request, $assignedTask);
                Log:info($request);
            } elseif ($request->filled('shooting_location')) {
                $this->updateShooting($request, $assignedTask);
            }

            DB::commit();

            return response()->json([
                "status" => "success",
                "assignedTask" => $assignedTask,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating assigned task: ' . $e->getMessage());

            return response()->json([
                "status" => "error",
                "message" => "An error occurred while processing the request.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    private function updateDesign($request, $assignedTask)
    {
        $design = $assignedTask->design()->first();
        if (!$design) {
            $design = new Design();
            $assignedTask->design()->save($design);
        }

        $designData = $request->only([
            'brand', 'type_of_media', 'deadline', 'content_writer_id', 'designer_id',
            'visual_copy', 'headline', 'body', 'objective', 'important_info',
            'taste_style', 'reference'
        ]);

        if ($request->hasFile('reference_photo')) {
            $photo = $request->file('reference_photo');
            $photoName = uniqid() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('file'), $photoName);
            $designData['reference_photo'] = $photoName;
        }

        $design->update($designData);

        if (
            $request->filled('visual_format') && $request->filled('aspect_ratio') &&
            $request->filled('width') && $request->filled('height') && $request->filled('resolution')
        ) {
            $artworkSizeData = $request->only([
                'visual_format', 'aspect_ratio', 'width', 'height', 'resolution'
            ]);

            $artworkSize = $design->artworkSizes()->updateOrCreate([], $artworkSizeData);
            $design->artworkSizes()->syncWithoutDetaching([$artworkSize->id]);
        }
    }


    private function updateShooting($request, $assignedTask)
    {
        $shooting = $assignedTask->shooting()->first();
        if (!$shooting) {
            $shooting = new Shooting();
            $assignedTask->shooting()->save($shooting);
        }

        $shootingData = $request->only([
            'shooting_location', 'type_detail', 'script_detail', 'scene_number', 'contact_name',
            'contact_phone', 'duration', 'type', 'client', 'date', 'time', 'video_shooting_project',
            'photo_shooting_project', 'arrive_office_on_time', 'transportation_charge', 'out_time',
            'in_time', 'crew_list', 'project_details'
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('file'), $fileName);
            $shootingData['document'] = $fileName;
        }

        $shooting->update($shootingData);

        if ($request->filled('shooting_accessories')) {
            $shootingCategories = json_decode($request->input('shooting_accessories'), true);
            if (is_array($shootingCategories)) {
                $categoryIds = [];
                foreach ($shootingCategories as $category) {
                    // Use updateOrCreate to update existing or create new records
                    $shootingCategory = ShootingAccessoryCategory::updateOrCreate(
                        ['accessory_name' => $category['accessory_name']],
                        [
                            'required_qty' => $category['required_qty'],
                            'taken_qty' => $category['taken_qty'],
                            'returned_qty' => $category['returned_qty']
                        ]
                    );
                    $categoryIds[] = $shootingCategory->id;
                }
                // Sync the pivot table with the updated accessory categories
                $shooting->shootingAccessoryCategories()->sync($categoryIds);
            } else {
                throw new \Exception('The shooting accessories field must be an array.');
            }
        }
    }



    //GET
    public function assignedTasks()
    {
        // Get all assigned tasks with their related customer, project, user, and shooting (including accessory categories)
        $assignedTasks = AssignedTask::with('customer', 'project', 'user', 'shooting.shootingAccessoryCategories')->get();

        // Initialize response array
        $response = [
            'status' => 'success',
            'assignedTasks' => [] // Note the plural form here
        ];

        // Loop through each assigned task
        foreach ($assignedTasks as $assignedTask) {
            // Make hidden fields and convert to array
            $taskData = $assignedTask->makeHidden(['design', 'shooting'])->toArray();

            // Add first design and its artwork sizes if available
            if (isset($assignedTask->design[0])) {
                $taskData['designData'] = $assignedTask->design[0];
                $taskData['designData']['artworkSizes'] = $assignedTask->design[0]->artworkSizes[0];
            } else {
                $taskData['designData'] = null;
                $taskData['artworkSizes'] = null;
            }

            // Add first shooting and its accessory categories if available
            if (isset($assignedTask->shooting[0])) {
                $taskData['shootingData'] = $assignedTask->shooting[0];
                $taskData['shootingData']['shooting_accessories'] = $assignedTask->shooting[0]->shootingAccessoryCategories;
                $shooting = $assignedTask->shooting[0];
                // Convert crew_list string to an actual array
                if (isset($shooting->crew_list)) {
                    $crewListString = $shooting->crew_list;
                    $crewListString = trim($crewListString, "[]");
                    $crewListArray = array_map('trim', explode(',', $crewListString));
                    $crewListArray = array_map(function($item) {
                        return trim($item, "'");
                    }, $crewListArray);
                    $shooting->crew_list = $crewListArray;
                }
            } else {
                $taskData['shootingData'] = null;
            }

            // Add the task data to the response array
            $response['assignedTasks'][] = $taskData;
        }

        return response()->json($response);
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
    // public function assignedTasksUpdate(AssignedTasksRequest $request, $id)
    // {
    //     $validatedData = $request->validated();
    //     // Check if 'status' is not provided or its value is null, then set default value
    //     if (!isset($validatedData['status']) || $validatedData['status'] === null) {
    //         $validatedData['status'] = 'pending';
    //     }
    //     $assignedTask = AssignedTask::findOrFail($id);
    //     $assignedTask->update($validatedData);
    //     return response()->json([
    //         "status" => "success",
    //         "assignedTask" => $assignedTask
    //     ]);
    // }
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
            $response['assignedTask']['designData']['artworkSizes'] = $assignedTask->design[0]->artworkSizes[0];
        } else {
            $response['assignedTask']['designData'] = null;
            $response['assignedTask']['artworkSizes'] = null;
        }

        // Add first shooting and its accessory categories if available
        if ($assignedTask && isset($assignedTask->shooting[0])) {
            $response['assignedTask']['shootingData'] = $assignedTask->shooting[0];
            $shooting = $assignedTask->shooting[0];
            $shooting['shooting_accessories'] = $shooting->shooting_accessory_categories;
            // unset($shooting->shooting_accessory_categories); // Remove the old key
            // Convert crew_list string to an actual array
            if (isset($shooting->crew_list)) {
                $crewListString = $shooting->crew_list;
                $crewListString = trim($crewListString, "[]");
                $crewListArray = array_map('trim', explode(',', $crewListString));
                $crewListArray = array_map(function($item) {
                    return trim($item, "'");
                }, $crewListArray);
                $shooting->crew_list = $crewListArray;
            }
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
