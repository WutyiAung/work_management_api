<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShootingAccessoryCategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','pivot'];
    public function shootings()
    {
        return $this->belongsToMany(Shooting::class, 'shooting_classification_pivots');
    }
}
