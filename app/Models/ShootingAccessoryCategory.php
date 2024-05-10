<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShootingAccessoryCategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function shootings()
    {
        return $this->belongsToMany(Shooting::class, 'shooting_classification_pivots');
    }
}
