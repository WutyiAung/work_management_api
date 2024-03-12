<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ProjectRequest;

class ProjectApiController extends Controller
{
    //create your project
    public function projectCreate(ProjectRequest $request){
        $validatedData = $request->validated();
        if($request->hasFile('document')){
            $contractPath = $request->file('document');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);

            // Add the filename to the validated data
            $validatedData['document'] = $contractPathName;
        }
        $data = Project::create($validatedData);
        return response()->json([
            "status" => "success",
            "data" => $data
        ]);
    }
    //all project Data
    public function project(){
        $projects = Project::with('customer','employee')->get();
        return response()->json([
            "status" => "success",
            "projects" => $projects
        ]);
    }

    //supervisor
    public function getSupervisor($id){
        $projects = Project::where('user_id',$id)->with('customer','employee')->get();
        return response()->json([
            "status" => "success",
            "projects" => $projects
        ]);
    }


    //Delete
    public function projectDelete($id){
        $project = Project::findOrFail($id);
        // Delete the main image if it exists
        if ($project->document) {
            File::delete(public_path('file/' . $project->document));
        }
        $project->delete();
        $projects = Project::get();
        return response()->json([
            "status" => "success",
            "projects" => $projects
        ]);
    }
    //Update
    public function projectUpdate(ProjectRequest $request,$id){
        $project = Project::findOrFail($id);
        $validatedData = $request->validated();
        if($request->hasFile('document')){
            $contractPath = $request->file('document');
            $contractPathName = uniqid().'_'.$contractPath->getClientOriginalName();
            $contractPath->move(public_path().'/file',$contractPathName);

            // Delete the old file if it exists
            if($project->document){
                // You may need to import the File facade at the top of your file:
                // use Illuminate\Support\Facades\File;
                File::delete(public_path().'/file/'.$project->document);
            }

            // Update the attachment path in the database
            $validatedData['document'] = $contractPathName;
        } else {
            // No file uploaded, retain the existing file path
            $validatedData['document'] = $project->document;
        }
        $project->update($validatedData);
        return response()->json([
            "status" => "success",
            "project" => $project
        ]);
    }
}
