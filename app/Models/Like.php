<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $guarded = []; // Cho phép nhận tất cả các trường

    public function posts() {
        return $this->belongsTo('App\Models\Post');
    }

    public function comment(){
        return $this->belongsTo('App\Models\Comment');
    }


    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function notifications(){
        return $this->hasMany('App\Models\Notification');
    }
}
