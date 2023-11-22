<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = []; // Cho phép nhận tất cả các trường

    public function post(){
        return $this->belongsTo('App\Models\Post');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function notifications(){
        return $this->hasMany('App\Models\Notification');
    }

    public function like(){
        return $this->hasMany('App\Models\Like');
    }
}
