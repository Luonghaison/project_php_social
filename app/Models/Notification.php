<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user(){
        return $this->$this->belongsTo('App\Models\User');
    }

    public function like(){
        return $this->$this->belongsTo('App\Models\Like');
    }

    public function comment(){
        return $this->$this->belongsTo('App\Models\Comment');
    }
}
