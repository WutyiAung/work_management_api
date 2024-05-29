<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShootingCategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['shootingAccessory'];
    public function shootingAccessory(){
        return $this->hasMany(ShootingAccessory::class);
    }
}
