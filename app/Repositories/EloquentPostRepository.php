<?php

namespace App\Repositories;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EloquentPostRepository
{
    protected $post;
    protected $user;

    /**
     * @param $post
     */
    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    public function create(array $data)
    {
        return $this->post->create($data);
    }

    public function info($id)
    {
        $post = $this->post->find($id);
        $countLike = Like::where('likeable_id', $id)->count();
        $countComment = $post->comments->count();
        $conttent = $this->searchById($id);
        $comment = $post->comments->all();
        return [
            'countLike' => $countLike,
            'countComment' => $countComment,
            'conttent' => $conttent,
            'comment' => $comment
        ];
    }

    public function infoMyPost()
    {

        $user = Auth::user();
        $allPost = $user->posts->where('approval_at', '!=', null)->paginate(10);
        $postInfo = [];

        if ($allPost->isEmpty()) {
            return [
                'message' => 'No posts have been created yet'
            ];
        }
        foreach ($allPost as $post) {
            $id = $post->id;
            $countLike = Like::where('likeable_id', $id)->count();
            $countComment = $post->comments->count();
            $content = $post;
            $comment = $post->comments->all();

            $postInfo[] = [
                'countLike' => $countLike,
                'countComment' => $countComment,
                'content' => $content,
                'comment' => $comment
            ];
        }
        return $postInfo;
    }

    public function infoMyPostsAndFriendsPosts()
    {
        $user = Auth::user();

        $posts = $user->posts->where('approval_at', '!=', null)
            ->union($user->friendsOfMine->pluck('posts')->flatten())
            //pluck để trích xuất thông tin
            //unicon để kết hợp vs 1 danh sách khác
            //flatten để làm phẳng danh sách các danh sách bài post của bạn bè thành một danh sách bài post duy nhất
            ->unique('id')
            ->paginate(10);

        $postInfo = [];

        if ($posts->isEmpty()) {
            return [
                'message' => 'No posts have been created yet'
            ];
        }

        foreach ($posts as $post) {
            $id = $post->id;
            $countLike = Like::where('likeable_id', $id)->count();
            $countComment = $post->comments->count();
            $content = $post;
            $comment = $post->comments->all();

            $postInfo[] = [
                'countLike' => $countLike,
                'countComment' => $countComment,
                'content' => $content,
                'comment' => $comment
            ];
        }

        return $postInfo;
    }

    //Lấy ra danh sách tất cả các post chưa được phê duyệt
    public function AllPostNoAccess()
    {
        $posts = $this->post->where('approval_at', '=', null)->paginate(10);

        $postInfo = [];

        if ($posts->isEmpty()) {
            return [
                'message' => 'No posts have been created yet'
            ];
        }

        foreach ($posts as $post) {
            $id = $post->id;
            $countLike = Like::where('likeable_id', $id)->count();
            $countComment = $post->comments->count();
            $content = $post;
            $comment = $post->comments->all();

            $postInfo[] = [
                'countLike' => $countLike,
                'countComment' => $countComment,
                'content' => $content,
                'comment' => $comment
            ];
        }

        return $postInfo;
    }

    //Lấy ra danh sách tất cả các post đã được phê duyệt
    public function AllPostAccess()
    {
        $posts = $this->post->where('approval_at', '!=', null)->paginate(10);

        $postInfo = [];

        if ($posts->isEmpty()) {
            return [
                'message' => 'No posts have been created yet'
            ];
        }

        foreach ($posts as $post) {
            $id = $post->id;
            $countLike = Like::where('likeable_id', $id)->count();
            $countComment = $post->comments->count();
            $content = $post;
            $comment = $post->comments->all();

            $postInfo[] = [
                'countLike' => $countLike,
                'countComment' => $countComment,
                'content' => $content,
                'comment' => $comment
            ];
        }
        return $postInfo;
    }

    public function PostReviewer($id)
    {
        $post = $this->post->find($id);
        $post->approval_at = now();
        $post->save();
        return $post;
    }

    public function delete($id)
    {
        $post = $this->post->find($id);
        $post->delete($id);
        return true;
    }

    public function searchById($id)
    {
        return $post = $this->post->find($id);
    }

    public function update($id, $data)
    {
        $post = $this->post->find($id);
        $post->update($data);
        return $post;
    }
}
