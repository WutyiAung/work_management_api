<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoEditing extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];
    public function highLight()
    {
        return $this->belongsToMany(HighLight::class,'video_editing_high_lights');
    }
}
