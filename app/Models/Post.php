<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded = []; // Cho phép nhận tất cả các trường


    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function likes(){
        return $this->hasMany('App\Models\Like');
    }

    public function comments(){
        return $this->hasMany('App\Models\Comment');
    }

    public function saves(){
        return $this->hasMany('App\Models\Save');
    }

    public function tags(){
        return $this->hasMany('App\Models\Tag');
    }

    public function images(){
        return $this->hasMany('App\Models\Image');
    }

}
