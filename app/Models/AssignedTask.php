<?php

namespace App\Models;

use App\Models\User;
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
