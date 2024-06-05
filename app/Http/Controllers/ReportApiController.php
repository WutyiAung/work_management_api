<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ReportRequest;
use App\Models\AssignedTask;
use Illuminate\Support\Facades\File;

class ReportApiController extends Controller
{
    //Create
    public function reportCreate(ReportRequest $request){
        $assignedTask = AssignedTask::where('id',$request->assigned_task_id)->first();
        $assignedTask->status = 'inProgress';
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
        $validatedData['status'] = $validatedData['status'] ?? 'pending';
        $reports = Report::create($validatedData);
        return response()->json([
            "status" => "success",
            "reports" => $reports
        ]);
    }
    public function report(){
        $report = Report::with('project','customer','task')->get();
        return response()->json([
            "status" => "success",
            "reports" => $report
        ]);
    }
    public function reportUpdate(ReportRequest $request, $id){
        $report = Report::findOrFail($id);
        $validatedData = $request->validated();
        if($request->hasFile('attachment_path')){
            $contractPath = $request->file('attachment_path');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);

            // Delete the old file if it exists
            if($report->attachment_path){
                // You may need to import the File facade at the top of your file:
                // use Illuminate\Support\Facades\File;
                File::delete(public_path().'/file/'.$report->attachment_path);
            }

            // Update the attachment path in the database
            $validatedData['attachment_path'] = $contractPathName;
        } else {
            // No file uploaded, retain the existing file path
            $validatedData['attachment_path'] = $report->attachment_path;
        }

        if($request->hasFile('photo_path')){
            $contractPath = $request->file('photo_path');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);

            // Delete the old file if it exists
            if($report->photo_path){
                // You may need to import the File facade at the top of your file:
                // use Illuminate\Support\Facades\File;
                File::delete(public_path().'/file/'.$report->photo_path);
            }

            // Update the attachment path in the database
            $validatedData['photo_path'] = $contractPathName;
        } else {
            // No file uploaded, retain the existing file path
            $validatedData['photo_path'] = $report->photo_path;
        }

        if($request->hasFile('video_path')){
            $contractPath = $request->file('video_path');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);

            // Delete the old file if it exists
            if($report->video_path){
                // You may need to import the File facade at the top of your file:
                // use Illuminate\Support\Facades\File;
                File::delete(public_path().'/file/'.$report->video_path);
            }

            // Update the attachment path in the database
            $validatedData['video_path'] = $contractPathName;
        } else {
            // No file uploaded, retain the existing file path
            $validatedData['video_path'] = $report->video_path;
        }

        $validatedData['status'] = $validatedData['status'] ?? 'pending';

        // Update the report with the validated data
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
    public function index($id){
        $report = Report::where('assigned_task_id',$id)->first();
        return response()->json([
            "status" => 200,
            "report" => $report
        ]);
    }
    public function reportsEmployee($id){
        $reports = Report::where('user_id',$id)->get();
        return response()->json([
            "status" => 200,
            "reports" => $reports
        ]);
    }
    public function reportsTask($id){
        $reports = Report::where('assigned_task_id',$id)->first();
        return response()->json([
            "status" => 200,
            "reports" => $reports
        ]);
    }
}
