<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $appends = ['imageUrl','videoUrl','documentUrl'];
    protected $with = ['project','customer','task'];
    public function getImageUrlAttribute()
    {
        return asset('file/' . $this->photo_path);
    }
    public function getVideoUrlAttribute(){
        return asset('file/'. $this->video_path);
    }
    public function getDocumentUrlAttribute(){
        return asset('file/'. $this->attachment_path);
    }
    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function task(){
        return $this->belongsTo(AssignedTask::class,'assigned_task_id');
    }
}
