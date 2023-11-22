<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Notification;
use App\Repositories\CommentRepository;
use App\Repositories\EloquentPostRepository;
use App\Repositories\LikeRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    protected $postRepository;
    protected $likeRepository;
    protected $commentRepository;

    /**
     * @param $postRepository
     * @param $likeRepository
     * @param $commentRepository
     */
    public function __construct(EloquentPostRepository $postRepository,LikeRepository $likeRepository,CommentRepository $commentRepository)
    {
        $this->postRepository = $postRepository;
        $this->likeRepository = $likeRepository;
        $this->commentRepository = $commentRepository;
    }


    /**
 * Display a listing of the resource.
 */
public function index()
{
    //
}

//Xem bài viết đã được like chưa,nếu chưa like thi cho like còn tồn tại rồi bấm vô sẽ hủy like
public
function handleLike($type, $id)
{

    $existing_like = Like::whereLikeableType($type)->whereLikeableId($id)->whereUserId(Auth::id())->first();

    if (!$existing_like) {
        $like = Like::create([
            'user_id' => Auth::user()->id,
            'likeable_id' => $id,
            'likeable_type' => $type,
        ]);
    } else {
        Like::whereLikeableType($type)->whereLikeableId($id)->whereUserId(Auth::id())->delete();
    }
}

//Tạo thông báo like gửi đến người dùng.Nếu user k phải người đăng bài sẽ tạo ra thông báo like
public function likePost($id)
{
    $post = $this->postRepository->searchById($id);
    if ($post) {
        $this->handleLike('APP/Post', $id);
        //sau khi chạy handle nếu like trong database sẽ có like và mình sẽ gửi thông báo đi
        if ($like = $this->likeRepository->check($id)) {
            if (!Notification::where('user_id', $post->user_id)
                ->where('from', Auth::user()->id)
                ->where('notification_type', 'APP/Like-post')
                ->where('seen', 0)->first()) {
                Notification::create([
                    'user_id' => $post->user_id,
                    'from' => Auth::user()->id,
                    'notification_type' => 'APP/Like-post',
                    'notification_id' => $id,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã Like bài viêt và gửi thông báo thành công'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Thông báo đã được gửi trước đó'
                ], Response::HTTP_OK);
            }
        } else {
            return response()->json([
                'message' => 'Hủy like thành công'
            ], Response::HTTP_OK);
        }

    } else {
        return response()->json([
            'message' => 'Không tìm thấy bài post'
        ], Response::HTTP_NOT_FOUND);
    }
}

public
function likeComment($id)
{
    $comment = $this->commentRepository->searchById($id);
    if ($comment) {
        $this->handleLike('APP/Like-Comment', $id);
        //sau khi chạy handle nếu like trong database sẽ có like và mình sẽ gửi thông báo đi
        if ($like = $this->likeRepository->check($id)) {
            if (!Notification::where('user_id', $comment->user_id)
                ->where('from', Auth::user()->id)
                ->where('notification_type', 'APP/Like-Comment')
                ->where('seen', 0)->first()) {
                Notification::create([
                    'user_id' => $comment->user_id,
                    'from' => Auth::user()->id,
                    'notification_type' => 'APP/Like-Comment',
                    'notification_id' => $id,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã Like comment và gửi thông báo thành công'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Thông báo đã được gửi trước đó'
                ], Response::HTTP_OK);
            }
        } else {
            return response()->json([
                'message' => 'Hủy like thành công'
            ], Response::HTTP_OK);
        }

    } else {
        return response()->json([
            'message' => 'Không tìm thấy bài post'
        ], Response::HTTP_NOT_FOUND);
    }
}

/**
 * Store a newly created resource in storage.
 */
public
function store(Request $request)
{
    //
}

/**
 * Display the specified resource.
 */
public
function show(string $id)
{
    //
}

/**
 * Update the specified resource in storage.
 */
public
function update(Request $request, string $id)
{
    //
}

/**
 * Remove the specified resource from storage.
 */
public
function destroy(string $id)
{
    //
}
}
