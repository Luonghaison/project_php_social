<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\User\EloquentUserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    protected $userRepository;

    /**
     * @param $userRepository
     */
    public function __construct(EloquentUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function sendRequest($id)
    {
        $user = $this->userRepository->findById($id);
        $loggedInUser = Auth::user();

        if ($loggedInUser->id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi lời mời kết bạn với chính mình'
            ], Response::HTTP_CONFLICT);
        }

        $friendRequests = $this->userRepository->friendRequests();
        $pendingRequests = $this->userRepository->friendRequestsPending();

        if ($friendRequests->contains($user) || $pendingRequests->contains($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu kết bạn đã tồn tại'
            ], Response::HTTP_CONFLICT);
        }

        // Tiếp tục xử lý khi không có yêu cầu kết bạn tồn tại
        $this->userRepository->addFriend($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi yêu cầu kết bạn thành công'
        ], Response::HTTP_OK);
    }

    public function CancelRequest($id){
        $user = $this->userRepository->findById($id);
        $loggedInUser = Auth::user();
        $pendingRequests = $this->userRepository->friendRequestsPending();


        if ($loggedInUser->id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy yêu cầu kết bạn với chính mình'
            ], Response::HTTP_CONFLICT);
        }
        if ($pendingRequests->contains($user)){
            $this->userRepository->removeFriend($user);
            return response()->json([
                'success' => true,
                'message' => 'Hủy yêu cầu kết bạn thành công'
            ], Response::HTTP_CONFLICT);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy yêu cầu kết bạn với người chưa gửi yaau cầu kết bạn với bạn'
            ], Response::HTTP_CONFLICT);
        }
    }

    public function removeFriend($id){

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
