<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Carbon\Carbon;
use App\Models\AssignedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\File;
use App\Models\ShootingAccessoryCategory;


class ReportApiController extends Controller
{
    //Create
    public function reportCreate(ReportRequest $request)
    {
        $assignedTask = AssignedTask::where('id',$request->assigned_task_id)->with('shooting')->first();
        // $assignedTask->status = $request->status;
        $assignedTask->progress = $request->progress;
        $assignedTask->is_reported = 1;
        // Check if progress is 100, and if so, set status to 'done'
        if ($request->progress == '100') {
            $request['status'] = 'done';  // Set request status to 'done'
            $assignedTask->status = 'done';  // Set assigned task status to 'done'
        } else {
            $assignedTask->status = $request->status;
        }
        $assignedTask->save();
        $validatedData = $request->validated();
        if($request->hasFile('attachment_path')){
            $contractPath = $request->file('attachment_path');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);

            // Add the filename to the validated data
            $validatedData['attachment_path'] = $contractPathName;
        }

        if($request->hasFile('photo_path')){
            $quotationPath = $request->file('photo_path');
            $quotationPathName = uniqid().'_'.$quotationPath->getClientOriginalName();
            $quotationPath->move(public_path().'/file',$quotationPathName);

            // Add the filename to the validated data
            $validatedData['photo_path'] = $quotationPathName;
        }

        if($request->hasFile('video_path')){
            $invoicePath = $request->file('video_path');
            $invoicePathName = uniqid().'_'.$invoicePath->getClientOriginalName();
            $invoicePath->move(public_path().'/file',$invoicePathName);

            // Add the filename to the validated data
            $validatedData['video_path'] = $invoicePathName;
        }
        if ($request->filled('shooting_accessories')) {
            $this->updateShootingAccessory($request, $assignedTask);
        }
        $validatedData['status'] = $validatedData['status'] ?? 'pending';
        $reports = Report::create($validatedData);
        return response()->json([
            "status" => "success",
            "reports" => $reports
        ]);
    }
    // Update Shooting Accessories
    private function updateShootingAccessory($request, $assignedTask)
    {
        $shooting = $assignedTask->shooting()->firstOrCreate([]);
        $shootingCategories = json_decode($request->input('shooting_accessories'), true);
        if (!is_array($shootingCategories)) {
            throw new \Exception('The shooting accessories field must be an array.');
        }

        $categoryIds = [];
        foreach ($shootingCategories as $category) {
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

        $shooting->shootingAccessoryCategories()->sync($categoryIds);
        Log::info($request);
    }
    // public function index(Request $request)
    // {
    //     $taskId = $request->query('task_id');
    //     $id = $request->query('id');
    //     $employeeId = $request->query('employee_id');
    //     $fromDate = $request->query('from_date');
    //     $toDate = $request->query('to_date');

    //     if ($id && $id !== 'undefined' && $id !== 'null') {
    //         $report = Report::with('task.shooting.shootingAccessoryCategories')->find($id);
    //         if (!$report) {
    //             return response()->json([
    //                 "status" => 404,
    //                 "message" => "Report not found"
    //             ], 404);
    //         }
    //         // If shootingData is not empty, get the first item
    //         $report->task->shootingData = $report->task->shooting->isNotEmpty() ? $report->task->shooting->first() : null;
    //         unset($report->task->shooting);

    //         // Rename shootingAccessoryCategories to shooting_accessories
    //         if ($report->task->shootingData) {
    //             $report->task->shootingData->shooting_accessories = $report->task->shootingData->shootingAccessoryCategories;
    //             unset($report->task->shootingData->shootingAccessoryCategories);
    //         }
    //         // Convert is_reported field to boolean
    //         $report->task->is_reported = $report->task->is_reported === "1";

    //         return response()->json([
    //             "status" => "success",
    //             "report" => $report
    //         ]);
    //     }

    //     $query = Report::with('task.shooting.shootingAccessoryCategories');
    //     if ($taskId && $taskId !== 'undefined' && $taskId !== 'null') {
    //         $query->where('assigned_task_id', $taskId);
    //     }
    //     if ($employeeId && $employeeId !== 'undefined' && $employeeId !== 'null') {
    //         $query->where('user_id', $employeeId);
    //     }
    //     if ($fromDate && $fromDate !== 'undefined' && $fromDate !== 'null') {
    //         $fromDate = Carbon::parse($fromDate)->startOfDay();
    //         if ($toDate && $toDate !== 'undefined' && $toDate !== 'null') {
    //             $toDate = Carbon::parse($toDate)->endOfDay();
    //             $query->whereBetween('created_at', [$fromDate, $toDate]);
    //         } else {
    //             $query->whereDate('created_at', $fromDate);
    //         }
    //     }
    //     $reports = $query->get();
    //     if ($reports->isEmpty()) {
    //         return response()->json([
    //             "status" => 404,
    //             "message" => "No reports found"
    //         ], 404);
    //     }
    //     $reports->each(function ($report) {
    //         // If shootingData is not empty, get the first item
    //         $report->task->shootingData = $report->task->shooting->isNotEmpty() ? $report->task->shooting->first() : null;
    //         unset($report->task->shooting);

    //         // Rename shootingAccessoryCategories to shooting_accessories
    //         if ($report->task->shootingData) {
    //             $report->task->shootingData->shooting_accessories = $report->task->shootingData->shootingAccessoryCategories;
    //             unset($report->task->shootingData->shootingAccessoryCategories);
    //         }
    //     });
    //     return response()->json([
    //         "status" => "success",
    //         "reports" => $reports
    //     ]);
    // }
    // use Carbon\Carbon;
    // use Illuminate\Http\Request;
    // use App\Models\Report;

    public function index(Request $request)
    {
        $taskId = $request->query('task_id');
        $id = $request->query('id');
        $employeeId = $request->query('employee_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        if ($id && $id !== 'undefined' && $id !== 'null') {
            $report = Report::with('task.shooting.shootingAccessoryCategories')->find($id);
            if (!$report) {
                return response()->json([
                    "status" => 404,
                    "message" => "Report not found"
                ], 404);
            }
            $this->processReport($report);
            return response()->json([
                "status" => "success",
                "report" => $report
            ]);
        }

        $query = Report::with('task.shooting.shootingAccessoryCategories');
        if ($taskId && $taskId !== 'undefined' && $taskId !== 'null') {
            $query->where('assigned_task_id', $taskId);
        }
        if ($employeeId && $employeeId !== 'undefined' && $employeeId !== 'null') {
            $query->where('user_id', $employeeId);
        }
        if ($fromDate && $fromDate !== 'undefined' && $fromDate !== 'null') {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            if ($toDate && $toDate !== 'undefined' && $toDate !== 'null') {
                $toDate = Carbon::parse($toDate)->endOfDay();
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } else {
                $query->whereDate('created_at', $fromDate);
            }
        }
        $reports = $query->get();
        if ($reports->isEmpty()) {
            return response()->json([
                "status" => 404,
                "message" => "No reports found"
            ], 404);
        }
        $reports->each(function ($report) {
            $this->processReport($report);
        });
        return response()->json([
            "status" => "success",
            "reports" => $reports
        ]);
    }

    private function filterQuery($queryParam)
    {
        return ($queryParam && $queryParam !== 'undefined' && $queryParam !== 'null') ? $queryParam : null;
    }

    private function processReport(&$report)
    {
        // If shootingData is not empty, get the first item
        $report->task->shootingData = $report->task->shooting->isNotEmpty() ? $report->task->shooting->first() : null;
        unset($report->task->shooting);

        // Rename shootingAccessoryCategories to shooting_accessories
        if ($report->task->shootingData) {
            $report->task->shootingData->shooting_accessories = $report->task->shootingData->shootingAccessoryCategories;
            unset($report->task->shootingData->shootingAccessoryCategories);
        }

        // Convert is_reported field to boolean
        $report->task->is_reported = $report->task->is_reported === 1;

        // Generate URLs for file paths
        $report->imageUrl = url('file/' . $report->photo_path);
        $report->videoUrl = url('file/' . $report->video_path);
        $report->documentUrl = url('file/' . $report->attachment_path);

        // If the report has a project, generate the file URL
        if (isset($report->project)) {
            $report->project->fileURL = url('file/' . $report->project->document);
        }

        // If the report has a shootingData, generate the file URL
        if (isset($report->task->shootingData)) {
            $report->task->shootingData->fileURL = url('file/' . $report->task->shootingData->document);
        }

        // If the report has a user, generate the image URL
        if (isset($report->user)) {
            $report->user->imgURL = url('file/' . $report->user->photo_path);
        }
    }

    public function reportUpdate(ReportRequest $request, $id)
    {
        $report = Report::findOrFail($id);
        $validatedData = $request->validated();
        if($request->hasFile('attachment_path')){
            $contractPath = $request->file('attachment_path');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);
            if($report->attachment_path){
                File::delete(public_path().'/file/'.$report->attachment_path);
            }
            $validatedData['attachment_path'] = $contractPathName;
        } else {
            $validatedData['attachment_path'] = $report->attachment_path;
        }
        if($request->hasFile('photo_path')){
            $contractPath = $request->file('photo_path');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);
            if($report->photo_path){
                File::delete(public_path().'/file/'.$report->photo_path);
            }
            $validatedData['photo_path'] = $contractPathName;
        } else {
            $validatedData['photo_path'] = $report->photo_path;
        }
        if($request->hasFile('video_path')){
            $contractPath = $request->file('video_path');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);
            if($report->video_path){
                File::delete(public_path().'/file/'.$report->video_path);
            }
            $validatedData['video_path'] = $contractPathName;
        } else {
            $validatedData['video_path'] = $report->video_path;
        }
        $assignedTask = AssignedTask::where('id',$report->assigned_task_id)->with('shooting')->first();
        $assignedTask->status = $request->status;
        $assignedTask->progress = $request->progress;
        $assignedTask->save();
        if ($request->filled('shooting_accessories')) {
            $this->updateShootingAccessory($request, $assignedTask);
        }
        $validatedData['status'] = $validatedData['status'] ?? 'pending';
        $report->update($validatedData);
        return response()->json([
            "status" => "success",
            "report" => $report
        ]);
    }
    public function reportDelete($id)
    {
        Report::findOrFail($id)->delete();
        $reports = Report::get();
        return response()->json([
            "status" => "success",
            "reports" => $reports
        ]);
    }
}
