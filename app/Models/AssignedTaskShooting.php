<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedTaskShooting extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function shootingClassificationPivots()
    {
        return $this->belongsToMany(ShootingClassificationPivot::class, 'assigned_task_shooting_shooting_classification', 'assigned_task_shooting_id', 'shooting_classification_id');
    }
}
