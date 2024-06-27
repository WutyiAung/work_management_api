<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighLight extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function videoEditing()
    {
        return $this->belongsToMany(VideoEditing::class,'video_editing_high_lights');
    }
}