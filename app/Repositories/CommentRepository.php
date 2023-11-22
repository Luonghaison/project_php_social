<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class CommentRepository
{
protected $comment;

    /**
     * @param $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function create($body, $post_id)
    {
        $data = [
            'body' => $body,
            'user_id' => Auth::user()->id,
            'post_id' => $post_id
        ];

        $this->comment->create($data);
    }

    public function searchById($id){
        return $this->comment->find($id);
    }

    public function info($id)
    {
        $comment = $this->comment->find($id);
        $countLike = Like::where('likeable_id', $id)->count();

        return [
            'countLike' => $countLike,
            'comment' => $comment,
        ];
    }

}
