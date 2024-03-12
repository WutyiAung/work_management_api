<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $appends = ['fileURL'];
    public function getFileUrlAttribute()
    {
        return asset('file/' . $this->document);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function employee(){
        return $this->belongsTo(User::class,'user_id');
    }
}
