<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Notification;
use App\Repositories\CommentRepository;
use App\Repositories\EloquentPostRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected $postRepository;
    protected $commentRepository;

    public function __construct(EloquentPostRepository $post, CommentRepository $commentRepository)
    {
        $this->postRepository = $post;
                $this->commentRepository = $commentRepository;

    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $comment = $this->commentRepository->info($id);
            return response()->json([
                'success' => true,
                'body' => $comment
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $commentRequest, $id)
    {
        $post = $this->postRepository->searchById($id);
        $body = $commentRequest->input('body');
        if ($post) {
            $this->commentRepository->create($body, $id);
//Gửi thông báo đi
            if ($post->user_id !== Auth::user()->id) {
                Notification::create([
                    'user_id' => $post->user_id,
                    'from' => Auth::user()->id,
                    'notification_type' => 'APP/Comment-post',
                    'notification_id' => $id,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'bình luận bài viết và gửi thông báo thành công',
                    'comment'=>$body,
                    'user_id'=>$post->user_id,
                    'post_id'=>$id
                ], Response::HTTP_OK);
            } else {
                return \response()->json([
                    'success' => true,
                    'message' => 'Người viết bài đã bình luận bài viết của mình',
                    'comment'=>$body,
                    'user_id'=>$post->user_id,
                    'post_id'=>$id
                ], Response::HTTP_OK);
            }

        } else {
            return \response()->json([
                'success' => false,
                'message' => 'The post not found',
            ], Response::HTTP_NOT_FOUND);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
