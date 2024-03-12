<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShootingAccessory extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function shootingCategory(){
        return $this->belongsTo(ShootingCategory::class);
    }
}
