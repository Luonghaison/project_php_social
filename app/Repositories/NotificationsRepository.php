<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationsRepository
{
protected $notifications;

    /**
     * @param $ntifications
     */
    public function __construct(Notification $notifications)
    {
        $this->notifications = $notifications;
    }

    public function getAllNotifications() {
        $notifications = $this->notifications
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return $notifications;
    }

    public function findById($id){
        return $this->notifications->find($id);
    }
}
