<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['path',
        'post_id',
        'user_id'];

    public function posts(){
        return $this->belongsTo('App\Models\Post');
    }
}
