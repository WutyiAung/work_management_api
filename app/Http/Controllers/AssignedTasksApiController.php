<?php

namespace App\Http\Controllers;

use App\Models\UiUx;
use App\Models\Design;
use App\Models\BackEnd;
use App\Models\Testing;
use App\Models\FrontEnd;
use App\Models\Shooting;
use App\Models\Deployment;
use App\Models\ArtworkSize;
use App\Models\AssignedTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ShootingAccessoryCategory;
use App\Http\Requests\AssignedTasksRequest;
use App\Models\ContentManagement;
use App\Models\ContentManagements;
use App\Models\HighLight;
use App\Models\PhotoEditing;
use App\Models\VideoEditing;

class AssignedTasksApiController extends Controller
{
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
            } elseif($request->filled('feature_type')){
                $frontEnd = $this->handleFrontEnd($request);
                $assignedTasks->frontEnd()->attach($frontEnd->id);
            } elseif($request->filled('use_case')){
                $backEnd = $this->handleBackEnd($request);
                $assignedTasks->backEnd()->attach($backEnd->id);
            } elseif($request->filled('customer_requirement')){
                $uiUx = $this->handleUiUx($request);
                $assignedTasks->uiUx()->attach($uiUx->id);
            } elseif($request->filled('testing_type')){
                $testing = $this->handleTesting($request);
                $assignedTasks->testing()->attach($testing->id);
            } elseif($request->filled('deployment_type')){
                $deployment = $this->handleDeployment($request);
                $assignedTasks->deployment()->attach($deployment->id);
            } elseif($request->filled('brand_name') && !$request->filled('video_editor')){
                $photoEditing = $this->handlePhotoEditing($request);
                $assignedTasks->photoEditing()->attach($photoEditing->id);
            } elseif($request->filled('brand_name') && $request->filled('video_editor')){
                $videoEditing = $this->handleVideoEditing($request);
                $assignedTasks->videoEditing()->attach($videoEditing->id);
            } elseif($request->filled('content_title')){
                $contentManagement = $this->handleContentManagement($request);
                $assignedTasks->contentManagement()->attach($contentManagement);
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
    private function handleFrontEnd($request)
    {
        $frontEndData = $request->only([
            'feature_type', 'reference_figma', 'detail_task', 'design_validation_detail', 'styling_detail',
            'api_integration'
        ]);
        $frontEnd = FrontEnd::create($frontEndData);
        return $frontEnd;
    }
    private function handleBackEnd($request)
    {
        $backEndData = $request->only([
            'use_case','crud_type','detail','database_migration','controller_name','routes','related_view'
        ]);
        $backEnd = BackEnd::create($backEndData);
        return $backEnd;
    }
    private function handleUiUx($request)
    {
        $uiUxData = $request->only([
            'customer_requirement','ui_type','reference_platform','ui_detail_task','ui_styling_detail','total_ui_screen','confirmed_ui_screen'
        ]);
        $uiUx = UiUx::create($uiUxData);
        return $uiUx;
    }
    private function handleTesting($request)
    {
        $testingData = $request->only([
            'testing_type','initial_test_brief','testing_issues','testing_overall','customer_comment',
        ]);
        $testing = Testing::create($testingData);
        return $testing;
    }
    private function handleDeployment($request)
    {
        $deploymentData = $request->only([
            'deployment_type','deployment_brief','server_type','instance_name','configuration','db_type','db_name','ip_and_port','username','project_type','dev_type','sub_domain','server_restart_after_deploy','apk_released_if_mobile','deployment_issues','deployment_overall'
        ]);
        $deployment = Deployment::create($deploymentData);
        return $deployment;
    }
    private function handlePhotoEditing($request)
    {
        $photoEditingData = $request->only([
            'brand_name','project_title','project_start_date','draft_deadline','final_deadline','account_executive','photo_retoucher','project_description','client_request_detail','number_of_retouch_photos','color_grade','editing_style','remark','editing_reference'
        ]);
        $photoEditing = PhotoEditing::create($photoEditingData);
        return $photoEditing;
    }
    private function handleVideoEditing($request)
    {
        $videoEditingData = $request->only([
            'brand_name','project_title','project_start_date','draft_deadline','final_deadline','account_executive','photo_retoucher','project_description','client_request_detail','number_of_retouch_photos','color_grade','editing_style','remark','editing_reference','video_editor','motion_text_effect','three_d_animation'
        ]);
        $videoEditing = VideoEditing::create($videoEditingData);
        if ($request->filled('high_light')) {
            $hightLights = json_decode($request->input('high_light'), true);
            if (is_array($hightLights)) {
                foreach ($hightLights as $hightLight) {
                    $hightLight = HighLight::create([
                        'time' => $hightLight['time'],
                        'description' => $hightLight['description'],
                        'remark' => $hightLight['remark'],
                    ]);
                    $videoEditing->highLight()->attach($hightLight->id);
                }
            } else {
                throw new \Exception('The hightLight field must be an array.');
            }
        }
        return $videoEditing;
    }
    private function handleContentManagement($request)
    {
        $data = $request->only([
            'content_title','notify_date','notify_time','content_description','is_seen'
        ]);
        $contentManagement = ContentManagements::create($data);
        return $contentManagement;
    }

    public function assignedTasksUpdate(AssignedTasksRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $assignedTask = AssignedTask::findOrFail($id);
            $validatedData = $request->validated();
            // $validatedData['status'] = $validatedData['status'] ?? 'pending';
            $assignedTask->update($validatedData);

            if ($request->filled('brand')) {
                $this->updateDesign($request, $assignedTask);
                Log:info($request);
            } elseif ($request->filled('shooting_location')) {
                $this->updateShooting($request, $assignedTask);
            } elseif ($request->filled('feature_type')) {
                $this->updateFrontEnd($request, $assignedTask);
            } elseif ($request->filled('use_case')) {
                $this->updateBackEnd($request, $assignedTask);
            } elseif ($request->filled('customer_requirement')){
                $this->updateUiUx($request, $assignedTask);
            } elseif ($request->filled('testing_type')){
                $this->updateTesting($request, $assignedTask);
            } elseif ($request->filled('deployment_type')){
                $this->updateDeployment($request, $assignedTask);
            } elseif($request->filled('brand_name') && !$request->filled('video_editor')){
                $this->updatePhotoEditing($request, $assignedTask);
            } elseif($request->filled('brand_name') && $request->filled('video_editor')){
                $this->updateVideoEditing($request, $assignedTask);
            } elseif($request->filled('content_title')){
                $this->updateContentManagement($request, $assignedTask);
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
    private function updateFrontEnd($request, $assignedTask)
    {
        $frontEnd = $assignedTask->frontEnd()->first();
        if (!$frontEnd) {
            $frontEnd = new FrontEnd();
            $assignedTask->frontEnd()->save($frontEnd);
        }

        $frontEndData = $request->only([
            'feature_type', 'reference_figma', 'detail_task', 'design_validation_detail', 'styling_detail',
            'api_integration'
        ]);
        $frontEnd->update($frontEndData);
    }
    private function updateBackEnd($request, $assignedTask)
    {
        $backEnd = $assignedTask->backEnd()->first();
        if (!$backEnd) {
            $backEnd = new BackEnd();
            $assignedTask->backEnd()->save($backEnd);
        }

        $backEndData = $request->only([
            'use_case','crud_type','detail','database_migration','controller_name','routes','related_view'
        ]);
        $backEnd->update($backEndData);
    }
    private function updateUiUx($request, $assignedTask)
    {
        $uiUx = $assignedTask->uiUx()->first();
        if(!$uiUx) {
            $uiUx = new UiUx();
            $assignedTask->uiUx()->save($uiUx);
        }

        $uiUxData = $request->only([
            'customer_requirement','ui_type','reference_platform','ui_detail_task','ui_styling_detail','total_ui_screen','confirmed_ui_screen'
        ]);
        $uiUx->update($uiUxData);
    }
    private function updateTesting($request, $assignedTask)
    {
        $testing = $assignedTask->testing()->first();
        if(!$testing) {
            $testing = new Testing();
            $assignedTask->testing()->save($testing);
        }

        $testingData = $request->only([
            'testing_type','initial_test_brief','testing_issues','testing_overall','customer_comment',
        ]);
        $testing->update($testingData);
    }
    private function updateDeployment($request, $assignedTask)
    {
        $deployment = $assignedTask->deployment()->first();
        if(!$deployment) {
            $deployment = new Deployment();
            $assignedTask->deployment()->save($deployment);
        }

        $deploymentData = $request->only([
            'deployment_type','deployment_brief','server_type','instance_name','configuration','db_type','db_name','ip_and_port','username','project_type','dev_type','sub_domain','server_restart_after_deploy','apk_released_if_mobile','deployment_issues','deployment_overall'
        ]);
        $deployment->update($deploymentData);
    }
    private function updatePhotoEditing($request, $assignedTask)
    {
        $photoEditing = $assignedTask->photoEditing()->first();
        if(!$photoEditing) {
            $photoEditing = new PhotoEditing();
            $assignedTask->photoEditing()->save($photoEditing);
        }
        $photoEditingData = $request->only([
            'brand_name','project_title','project_start_date','draft_deadline','final_deadline','account_executive','photo_retoucher','project_description','client_request_detail','number_of_retouch_photos','color_grade','editing_style','remark','editing_reference'
        ]);
        $photoEditing->update($photoEditingData);
    }
    private function updateVideoEditing($request, $assignedTask)
    {
        $videoEditing = $assignedTask->videoEditing()->first();
        if(!$videoEditing) {
            $videoEditing = new VideoEditing();
            $assignedTask->videoEditing()->save($videoEditing);
        }
        $videoEditingData = $request->only([
            'brand_name','project_title','project_start_date','draft_deadline','final_deadline','account_executive','photo_retoucher','project_description','client_request_detail','number_of_retouch_photos','color_grade','editing_style','remark','editing_reference','video_editor','motion_text_effect','three_d_animation'
        ]);
        $videoEditing->update($videoEditingData);
        if ($request->filled('high_light')) {
            $hightLights = json_decode($request->input('high_light'), true);
            if (is_array($hightLights)) {
                $hightLightIds = [];
                foreach ($hightLights as $hightLight) {
                    // Use updateOrCreate to update existing or create new records
                    $hightLightData = HighLight::updateOrCreate(
                        // ['time' => $hightLight['time']],
                        [
                            'time' => $hightLight['time'],
                            'description' => $hightLight['description'],
                            'remark' => $hightLight['remark'],
                        ]
                    );
                    $hightLightIds[] = $hightLightData->id;
                }
                // Sync the pivot table with the updated high light
                $videoEditing->highLight()->sync($hightLightIds);
            } else {
                throw new \Exception('The high light field must be an array.');
            }
        }
    }
    private function updateContentManagement($request, $assignedTask)
    {
        $contentManagement = $assignedTask->contentManagement()->first();
        if(!$contentManagement) {
            $contentManagement = new ContentManagements();
            $assignedTask->contentManagement()->save($contentManagement);
        }
        $contentManagementData = $request->only([
            'content_title','notify_date','notify_time','content_description','is_seen'
        ]);
        $contentManagement->update($contentManagementData);
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

            if (isset($assignedTask->frontEnd[0])) {
                $taskData['frontEndData'] = $assignedTask->frontEnd[0];
                $frontEnd = $assignedTask->frontEnd[0];
                // Convert crew_list string to an actual array
                if (isset($frontEnd->feature_type)) {
                    $stringData = $frontEnd->feature_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $frontEnd->feature_type = $arrayData;
                }
            } else {
                $taskData['frontEndData'] = null;
            }

            if (isset($assignedTask->backEnd[0])) {
                $taskData['backEndData'] = $assignedTask->backEnd[0];
            } else {
                $taskData['backEndData'] = null;
            }

            if (isset($assignedTask->uiUx[0])) {
                $taskData['uiUxData'] = $assignedTask->uiUx[0];
                $uiUx = $assignedTask->uiUx[0];
                // Convert crew_list string to an actual array
                if (isset($uiUx->ui_type)) {
                    $stringData = $uiUx->ui_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $uiUx->ui_type = $arrayData;
                }
            } else {
                $taskData['uiUxData'] = null;
            }

            if (isset($assignedTask->testing[0])) {
                $taskData['testingData'] = $assignedTask->testing[0];
            } else {
                $taskData['testingData'] = null;
            }

            if (isset($assignedTask->deployment[0])) {
                $taskData['deployment'] = $assignedTask->deployment[0];
                $taskData['deployment']['server_restart_after_deploy'] = $assignedTask->deployment[0]->server_restart_after_deploy === 1;
                $taskData['deployment']['apk_released_if_mobile'] = $assignedTask->deployment[0]->apk_released_if_mobile === 1;
                $deployment = $assignedTask->deployment[0];
                // Convert crew_list string to an actual array
                if (isset($deployment->project_type)) {
                    $stringData = $deployment->project_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $deployment->project_type = $arrayData;
                }
                if (isset($deployment->dev_type)) {
                    $stringData = $deployment->dev_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $deployment->dev_type = $arrayData;
                }
            } else {
                $taskData['deployment'] = null;
            }

            if (isset($assignedTask->photoEditing[0])) {
                $taskData['photoEditingData'] = $assignedTask->photoEditing[0];
                $photoEditing = $assignedTask->photoEditing[0];
                if (isset($photoEditing->photo_retoucher)) {
                    $stringData = $photoEditing->photo_retoucher;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $photoEditing->photo_retoucher = $arrayData;
                }
            } else {
                $taskData['photoEditingData'] = null;
            }

            if (isset($assignedTask->videoEditing[0])) {
                $taskData['videoEditingData'] = $assignedTask->videoEditing[0];
                $videoEditing = $assignedTask->videoEditing[0];
                $taskData['videoEditingData']['high_light'] = $assignedTask->videoEditing[0]->highLight;
                // Convert crew_list string to an actual array
                if (isset($videoEditing->video_editor)) {
                    $stringData = $videoEditing->video_editor;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $videoEditing->video_editor = $arrayData;
                }
            } else {
                $taskData['videoEditingData'] = null;
            }

            if (isset($assignedTask->contentManagement[0])) {
                $taskData['contentManagementData'] = $assignedTask->contentManagement[0];
                $taskData['contentManagementData']['is_seen'] = $assignedTask->contentManagement[0]->is_seen === 1;
            } else {
                $taskData['contentManagementData'] = null;
            }

            // Convert is_reported field to boolean
            $taskData['is_reported'] = $assignedTask->is_reported === 1;
            $taskData['is_done'] = $assignedTask->is_done === 1;
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

        if ($assignedTask && isset($assignedTask->frontEnd[0])) {
            $response['frontEndData'] = $assignedTask->frontEnd[0];
            $frontEnd = $assignedTask->frontEnd[0];
            // Convert crew_list string to an actual array
            if (isset($frontEnd->feature_type)) {
                $stringData = $frontEnd->feature_type;
                $stringData = trim($stringData, "[]");
                $arrayData = array_map('trim', explode(',', $stringData));
                $arrayData = array_map(function($item) {
                    return trim($item, "'");
                }, $arrayData);
                $frontEnd->feature_type = $arrayData;
            }
        } else {
            $response['frontEndData'] = null;
        }

        if ($assignedTask && isset($assignedTask->backEnd[0])) {
            $response['backEndData'] = $assignedTask->backEnd[0];
        } else {
            $response['backEndData'] = null;
        }

        if ($assignedTask && isset($assignedTask->uiUx[0])) {
            $response['uiUxData'] = $assignedTask->uiUx[0];
            $uiUx = $assignedTask->uiUx[0];
            // Convert crew_list string to an actual array
            if (isset($uiUx->ui_type)) {
                $stringData = $uiUx->ui_type;
                $stringData = trim($stringData, "[]");
                $arrayData = array_map('trim', explode(',', $stringData));
                $arrayData = array_map(function($item) {
                    return trim($item, "'");
                }, $arrayData);
                $uiUx->ui_type = $arrayData;
            }
        } else {
            $response['uiUxData'] = null;
        }

        if ($assignedTask && isset($assignedTask->testing[0])) {
            $response['testingData'] = $assignedTask->testing[0];
        } else {
            $response['testingData'] = null;
        }

        if ($assignedTask && isset($assignedTask->deployment[0])) {
            $response['deployment'] = $assignedTask->deployment[0];
            $response['deployment']['server_restart_after_deploy'] = $assignedTask->deployment[0]->server_restart_after_deploy === 1;
            $response['deployment']['apk_released_if_mobile'] = $assignedTask->deployment[0]->apk_released_if_mobile === 1;
            $deployment = $assignedTask->deployment[0];
            // Convert crew_list string to an actual array
            if (isset($deployment->project_type)) {
                $stringData = $deployment->project_type;
                $stringData = trim($stringData, "[]");
                $arrayData = array_map('trim', explode(',', $stringData));
                $arrayData = array_map(function($item) {
                    return trim($item, "'");
                }, $arrayData);
                $deployment->project_type = $arrayData;
            }
            if (isset($deployment->dev_type)) {
                $stringData = $deployment->dev_type;
                $stringData = trim($stringData, "[]");
                $arrayData = array_map('trim', explode(',', $stringData));
                $arrayData = array_map(function($item) {
                    return trim($item, "'");
                }, $arrayData);
                $deployment->dev_type = $arrayData;
            }
        } else {
            $response['deployment'] = null;
        }

        if ($assignedTask && isset($assignedTask->photoEditing[0])) {
            $response['photoEditingData'] = $assignedTask->photoEditing[0];
            $photoEditing = $assignedTask->photoEditing[0];
            if (isset($photoEditing->photo_retoucher)) {
                $stringData = $photoEditing->photo_retoucher;
                $stringData = trim($stringData, "[]");
                $arrayData = array_map('trim', explode(',', $stringData));
                $arrayData = array_map(function($item) {
                    return trim($item, "'");
                }, $arrayData);
                $photoEditing->photo_retoucher = $arrayData;
            }
        } else {
            $response['photoEditingData'] = null;
        }

        if ($assignedTask && isset($assignedTask->videoEditing[0])) {
            $response['videoEditingData'] = $assignedTask->videoEditing[0];
            $videoEditing = $assignedTask->videoEditing[0];
            $response['videoEditingData']['high_light'] = $assignedTask->videoEditing[0]->highLight;
            // Convert crew_list string to an actual array
            if (isset($videoEditing->video_editor)) {
                $stringData = $videoEditing->video_editor;
                $stringData = trim($stringData, "[]");
                $arrayData = array_map('trim', explode(',', $stringData));
                $arrayData = array_map(function($item) {
                    return trim($item, "'");
                }, $arrayData);
                $videoEditing->video_editor = $arrayData;
            }
        } else {
            $taskData['videoEditingData'] = null;
        }

        if ($assignedTask && isset($assignedTask->contentManagement[0])) {
            $response['contentManagementData'] = $assignedTask->contentManagement[0];
            $response['contentManagementData']['is_seen'] = $assignedTask->contentManagement[0]->is_seen === 1;
        } else {
            $response['contentManagementData'] = null;
        }

        $response['assignedTask']['is_reported'] = $assignedTask->is_reported === 1;
        $response['assignedTask']['is_done'] = $assignedTask->is_done === 1;
        return response()->json($response);
    }
    public function assignedTasksEmployee($id)
    {
        $assignedTasks = AssignedTask::where('user_id', $id)
            ->with(['customer', 'project', 'user', 'shooting.shootingAccessoryCategories'])
            ->get();
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

            if (isset($assignedTask->frontEnd[0])) {
                $taskData['frontEndData'] = $assignedTask->frontEnd[0];
                $frontEnd = $assignedTask->frontEnd[0];
                // Convert crew_list string to an actual array
                if (isset($frontEnd->feature_type)) {
                    $stringData = $frontEnd->feature_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $frontEnd->feature_type = $arrayData;
                }
            } else {
                $taskData['frontEndData'] = null;
            }

            if (isset($assignedTask->backEnd[0])) {
                $taskData['backEndData'] = $assignedTask->backEnd[0];
            } else {
                $taskData['backEndData'] = null;
            }

            if (isset($assignedTask->uiUx[0])) {
                $taskData['uiUxData'] = $assignedTask->uiUx[0];
                $uiUx = $assignedTask->uiUx[0];
                // Convert crew_list string to an actual array
                if (isset($uiUx->ui_type)) {
                    $stringData = $uiUx->ui_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $uiUx->ui_type = $arrayData;
                }
            } else {
                $taskData['uiUxData'] = null;
            }

            if (isset($assignedTask->testing[0])) {
                $taskData['testingData'] = $assignedTask->testing[0];
            } else {
                $taskData['testingData'] = null;
            }

            if (isset($assignedTask->deployment[0])) {
                $taskData['deployment'] = $assignedTask->deployment[0];
                $taskData['deployment']['server_restart_after_deploy'] = $assignedTask->deployment[0]->server_restart_after_deploy === 1;
                $taskData['deployment']['apk_released_if_mobile'] = $assignedTask->deployment[0]->apk_released_if_mobile === 1;
                $deployment = $assignedTask->deployment[0];
                // Convert crew_list string to an actual array
                if (isset($deployment->project_type)) {
                    $stringData = $deployment->project_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $deployment->project_type = $arrayData;
                }
                if (isset($deployment->dev_type)) {
                    $stringData = $deployment->dev_type;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $deployment->dev_type = $arrayData;
                }
            } else {
                $taskData['deployment'] = null;
            }

            if (isset($assignedTask->photoEditing[0])) {
                $taskData['photoEditingData'] = $assignedTask->photoEditing[0];
                $photoEditing = $assignedTask->photoEditing[0];
                if (isset($photoEditing->photo_retoucher)) {
                    $stringData = $photoEditing->photo_retoucher;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $photoEditing->photo_retoucher = $arrayData;
                }
            } else {
                $taskData['photoEditingData'] = null;
            }

            if (isset($assignedTask->videoEditing[0])) {
                $taskData['videoEditingData'] = $assignedTask->videoEditing[0];
                $videoEditing = $assignedTask->videoEditing[0];
                $taskData['videoEditingData']['high_light'] = $assignedTask->videoEditing[0]->highLight;
                // Convert crew_list string to an actual array
                if (isset($videoEditing->video_editor)) {
                    $stringData = $videoEditing->video_editor;
                    $stringData = trim($stringData, "[]");
                    $arrayData = array_map('trim', explode(',', $stringData));
                    $arrayData = array_map(function($item) {
                        return trim($item, "'");
                    }, $arrayData);
                    $videoEditing->video_editor = $arrayData;
                }
            } else {
                $taskData['videoEditingData'] = null;
            }

            if (isset($assignedTask->contentManagement[0])) {
                $taskData['contentManagementData'] = $assignedTask->contentManagement[0];
                $taskData['contentManagementData']['is_seen'] = $assignedTask->contentManagement[0]->is_seen === 1;
            } else {
                $taskData['contentManagementData'] = null;
            }

            // Convert is_reported field to boolean
            $taskData['is_reported'] = $assignedTask->is_reported === 1;
            $taskData['is_done'] = $assignedTask->is_done === 1;
            // Add the task data to the response array
            $response['assignedTasks'][] = $taskData;
        }
        return response()->json($response);
    }
}
