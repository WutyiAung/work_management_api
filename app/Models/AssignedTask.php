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
}
