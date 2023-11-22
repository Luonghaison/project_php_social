<?php

namespace App\Repositories\User;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function Illuminate\Support\Facades\Http;

class EloquentUserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

public function create(array $data)
{
    return $this->model->create($data);
}

public function findById($id){
        return $this->model->find($id);
}

public function login(array $data)
{
    $user = $this->model->where('email', $data['email'])->first();
    return $user;
}

public function changePassword($userId, $newPassword)
{
    $user = $this->model->find($userId);
    $user->update([
        'password' => Hash::make($newPassword)]);
}

    public function incrementLoginAttempts($id)
    {
        $user = $this->model->find($id);

        if ($user) {
            $dataToUpdate = [
                'login_attempts' => $user->login_attempts+1,
                'last_login_attempt' => now(),
            ];

            if ($dataToUpdate['login_attempts'] >= 5) {
                $dataToUpdate['locked_until'] = now()->addMinutes(5); // Mở khóa sau 5 phút
            }

            $user->update($dataToUpdate);
            return $user;
        }
    }

    public function resetLoginAttempts($id)
    {
        $user = $this->model->find($id);

        if ($user) {
            // Kiểm tra xem tài khoản có bị khoá không
            if ($user->locked_until && now() > $user->locked_until) {
                $user->update([
                    'login_attempts' => 0,
                    'locked_until' => null,
                ]);
            }
        }
    }

    //Lấy ra danh sách những thằng cha chưa đc xác nhận trong bảng friend
// Trong UserRepository
    public function friendRequests()
    {
        return $this->model->friendsOfMine()->wherePivot('accepted', false)->get();
    }

    public function friendRequestsPending()
    {
        return $this->model->friendOf()->wherePivot('accepted', false)->get();
    }

    public function addFriend($userId){
        $loggedInUserId = Auth::id();
        return $this->model->friendsOfMine()->attach($userId, ['user_id' => $loggedInUserId]);
    }

    public function removeFriend($user){
        $loggedInUserId = Auth::id();
        return $this->model->friendOf()->detach($user->id, ['user_id' => $loggedInUserId]);
    }

//    public function isFriendsWith(User $user){
//        return (bool) $this->friends()->where('id', $user->id)->count();
//    }
//
//    public function removeFriend(User $user){
//        return $this->friendOf()->detach($user->id);
//    }

}
