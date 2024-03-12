<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $guarded=[];
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
