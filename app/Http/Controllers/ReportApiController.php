<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Shooting;
use App\Models\AssignedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\File;
use App\Models\ShootingAccessoryCategory;

use Carbon\Carbon;
class ReportApiController extends Controller
{
    //Create
    public function reportCreate(ReportRequest $request){
        $assignedTask = AssignedTask::where('id',$request->assigned_task_id)->with('shooting')->first();
        $assignedTask->status = $request->status;
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

    public function index(Request $request)
    {
        $taskId = $request->query('task_id');
        $id = $request->query('id');
        $employeeId = $request->query('employee_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        if ($id && $id !== 'undefined' && $id !== 'null') {
            $report = Report::with(['task.shooting.shootingAccessoryCategories' => function($query) {
                $query->select('shooting_accessory_categories.id', 'shooting_accessory_categories.accessory_name', 'shooting_accessory_categories.required_qty', 'shooting_accessory_categories.taken_qty', 'shooting_accessory_categories.returned_qty');
            }])->find($id);

            if (!$report) {
                return response()->json([
                    "status" => 404,
                    "message" => "Report not found"
                ], 404);
            }

            // Transform the data
            $report->task->shooting_accessories = $report->task->shooting->flatMap(function($shooting) {
                return $shooting->shootingAccessoryCategories;
            });

            unset($report->task->shooting);

            return response()->json([
                "status" => "success",
                "report" => $report
            ]);
        }

        $query = Report::with(['task.shooting.shootingAccessoryCategories' => function($query) {
            $query->select('shooting_accessory_categories.id', 'shooting_accessory_categories.accessory_name', 'shooting_accessory_categories.required_qty', 'shooting_accessory_categories.taken_qty', 'shooting_accessory_categories.returned_qty');
        }]);

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

        // Transform the data for all reports
        $reports->each(function($report) {
            $report->task->shooting_accessories = $report->task->shooting->flatMap(function($shooting) {
                return $shooting->shootingAccessoryCategories;
            });

            unset($report->task->shooting);
        });

        return response()->json([
            "status" => "success",
            "reports" => $reports
        ]);
    }

    public function reportUpdate(ReportRequest $request, $id){
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
    public function reportDelete($id){
        Report::findOrFail($id)->delete();
        $reports = Report::get();
        return response()->json([
            "status" => "success",
            "reports" => $reports
        ]);
    }
}
