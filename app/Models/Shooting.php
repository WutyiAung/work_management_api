<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ShootingClassificationPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shooting extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['fileURL'];
    public function getFileUrlAttribute()
    {
        return asset('file/' . $this->document);
    }
    public function shootingAccessoryCategories()
    {
        return $this->belongsToMany(ShootingAccessoryCategory::class, 'shooting_classification_pivots');
    }

}
