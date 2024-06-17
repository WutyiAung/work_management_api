<?php

namespace App\Models;

use App\Models\UiUx;
use App\Models\User;
use App\Models\Design;
use App\Models\BackEnd;
use App\Models\Project;
use App\Models\Testing;
use App\Models\Customer;
use App\Models\FrontEnd;
use App\Models\Shooting;
use App\Models\Deployment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignedTask extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function design(){
        return $this->belongsToMany(Design::class,'assigned_task_designs');
    }
    public function shooting(){
        return $this->belongsToMany(Shooting::class,'assigned_task_shootings');
    }
    public function frontEnd(){
        return $this->belongsToMany(FrontEnd::class,'assigned_task_front_ends');
    }
    public function backEnd(){
        return $this->belongsToMany(BackEnd::class,'assigned_task_back_ends');
    }
    public function uiUx(){
        return $this->belongsToMany(UiUx::class,'assigned_task_ui_uxes');
    }
    public function testing(){
        return $this->belongsToMany(Testing::class,'assigned_task_testings');
    }
    public function deployment(){
        return $this->belongsToMany(Deployment::class,'assigned_task_deployments');
    }
    // Adding the deleting event to remove pivot table entries
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($assignedTask) {
            $assignedTask->design()->detach();
            $assignedTask->shooting()->detach();
        });
    }
}
