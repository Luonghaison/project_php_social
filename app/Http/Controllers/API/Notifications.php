<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\EloquentPostRepository;
use App\Repositories\LikeRepository;
use App\Repositories\NotificationsRepository;
use Illuminate\Http\Request;

class Notifications extends Controller
{
    protected $notificationRepository;

    public function __construct(NotificationsRepository $notificationsRepository)
    {
        $this->notificationRepository = $notificationsRepository;
    }


    /**
     * Display a listing of the resource.
     */
    //Hiển thị các thông báo của tài khoản đang đăng nhập
    public function index()
    {
        $notifications = $this->notificationRepository->getAllNotifications();
        $notificationMessages = [];

        foreach ($notifications as $notification) {
            $notificationType = $notification->notification_type;
            $message = '';
            $user = User::find($notification->from)->name;
            if ($notificationType === 'APP/Comment-post' && $notification->from != $notification->user_id) {
                $message = $user . ' đã bình luận bài viết của bạn';
            } elseif ($notificationType === 'APP/Like-post' && $notification->from != $notification->user_id) {
                $message = $user . ' đã like bài viết của bạn';
            } elseif ($notificationType === 'APP/Like-Comment' && $notification->from != $notification->user_id) {
                $message = $user . ' đã like comment của bạn';
            }

            $notificationMessages[] = [
                'id'=>$notification->id,
                'message' => $message,
                'seen' => $notification->seen,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $notificationMessages,
        ]);
    }

    //đánh dấu thông báo được đọc
    public function markAsSeen($notificationId)
    {
        $notification = $this->notificationRepository->findById($notificationId);

        if ($notification) {
            $notification->update(['seen' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as seen.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.'
            ], 404);
        }
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
