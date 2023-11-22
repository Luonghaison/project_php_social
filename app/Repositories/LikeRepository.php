<?php

namespace App\Repositories;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeRepository
{
protected $like;

    /**
     * @param $like
     */
    public function __construct(Like $like)
    {
        $this->like = $like;
    }

    public function check($id){
        return
        $this->like->whereLikeableType('APP/Post')->whereLikeableId($id)->whereUserId(Auth::user()->id)->first();
    }


}
