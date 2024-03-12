<?php

namespace App\Models;

use App\Models\Design;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArtworkSize extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function designs()
    {
        return $this->belongsToMany(Design::class, 'design_artwork_sizes');
    }
}
