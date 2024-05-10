<?php

namespace App\Models;

use App\Models\ArtworkSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Design extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $appends = ['imageURL'];
    public function getImageUrlAttribute()
    {
        return asset('file/' . $this->reference_photo);
    }
    public function artworkSizes()
    {
        return $this->belongsToMany(ArtworkSize::class, 'design_artwork_sizes');
    }
    public function assignedTask(){
        return $this->belongsToMany(AssignedTask::class,'assigned_task_designs');
    }
}
