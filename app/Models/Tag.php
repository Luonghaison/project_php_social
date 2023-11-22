<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name',
        'post_id'];

    public function posts(){
        return $this->belongsTo('App\Models\Post');
    }

}
