<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\LoginCollection;
use App\Http\Resources\RegisterCollection;
use App\Http\Resources\RegisterResource;
use App\Mail\ForgotPasswordMail;
use App\Mail\OtpMail;
use App\Models\User;
use App\Repositories\User\EloquentUserRepository;
use App\Repositories\User\UserRepositoryInterface;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    protected $userRepository;

    /**
     * @param $usersModel
     */
    public function __construct(EloquentUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(StoreRegisterRequest $request)
    {
        $data = $request->all();

        //Tạo ngẫu nhiên 1 OTP code:
        $otp_code = rand(1000, 9999);
        $otp_expires_at = now()->addMinutes(1);

        $data['otp_code'] = $otp_code;
        $data['otp_expires_at'] = $otp_expires_at;

        $userRegister = $this->userRepository->create($data);
        //Đây là mỗi khi đăng ký sẽ mặc định role là user
        $userRegister->assignRole('user');

        //Sử dụng laravel mail gửi mã
        Mail::to($userRegister->email)->send(new VerifyEmail($otp_code));

        $userResource = new RegisterResource($userRegister);

        return $this->sentsucessResponce($userResource, '登録が完了しました/Đăng ký tài khoản  thành công', Response::HTTP_OK);
    }

    //gửi lại mã code OTP vào mail cá nhân của tài khoản đăng nhập
    public function sendVerifyMail($email)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->email === $email) {
            $otp_code = rand(1000, 9999);
            $otp_expires_at = now()->addMinutes(1);

            // Truyền otp_code vào markdown email
            Mail::to($email)->send(new VerifyEmail($otp_code));

            $user->otp_code = $otp_code;
            $user->otp_expires_at = $otp_expires_at;

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Mail sent successfully'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User is not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    //Hàm kiểm tra xem mã OTP nhập vào có hợp lệ hay không,ợp lệ thì sửa lại email_verified_at
    public function verifiOTP($otp_code)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->otp_code === $otp_code && now()->lt($user->otp_expires_at)) {
                $user->email_verified_at = now();
                $user->save();
                return \response()->json([
                    'success' => true,
                    'message' => 'Valid OTP code'
                ], Response::HTTP_OK);
            } else {
                return \response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP code'
                ], Response::HTTP_UNAUTHORIZED);
            }
        } else {
            return \response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }


    //Đây là phần login dùng sanctum
//    public function login(UserLoginRequest $request)
//    {
//        $data = $request->all();
//
//        if (auth('web')->attempt($data)) {
//            $user = auth()->user();
//            $token = $user->createToken('taolason')->plainTextToken;
//
//            $userResource = new LoginCollection($user);
//            return \response()->json([
//                'user' => $userResource,
//                'token' => $token
//            ], Response::HTTP_OK);
//
//        } else {
//            return \response()->json([
//                'message' => 'エラーが発生しました/Đã có lỗi xảy ra'
//            ], Response::HTTP_NOT_FOUND);
//        }
//    }


//Đây là phần login dùng JWT
    public function login(UserLoginRequest $request)
    {
        $data = $request->all();
        $user = $this->userRepository->login($data);

        // Thử đăng nhập
        if (!$token = auth()->attempt($data)) {
            // Đăng nhập thất bại
            $this->userRepository->incrementLoginAttempts($user->id);

            if ($user->login_attempts >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Exceeded the maximum number of unsuccessful login attempts. Please try again later.'
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

            return response()->json([
                'success' => false,
                'message' => '[メールアドレスまたはパスワードが正しくありません。/Địa chỉ email hoặc mật khẩu không hợp lệ.]',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->locked_until && now() < $user->locked_until) {
            return response()->json([
                'success' => false,
                'message' => 'The account has been locked. Please try again later.'
            ], Response::HTTP_LOCKED);
        }

        // Đăng nhập thành công, reset lại số lần đăng nhập thất bại
        $this->userRepository->resetLoginAttempts($user->id);

        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản này chưa được xác thực/このアカウントは認証されていません.',
                'access_token' => $token,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'sucess' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], Response::HTTP_OK);
    }

    public function getMe()
    {
        try {
            $user = Auth::user()->name;
            return \response()->json([
                'success' => true,
                'name' => $user,
                'message' => 'Account name displayed successfully!'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return \response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return \response()->json([
                'success' => true,
                'message' => 'User logged out!'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return \response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }
    }


    public function changePassword(ResetPasswordRequest $resetPasswordRequest)
    {
        if (Auth::check()) {
            $data = $resetPasswordRequest->all();
            $user = Auth::user();
            if (Hash::check($data['old_password'], $user->password)) {
                $this->userRepository->changePassword($user->id, $data['password']);
                return \response()->json([
                    'success' => true,
                    'message' => 'Password successfully update'
                ], Response::HTTP_OK);
            } else return \response()->json([
                'success' => false,
                'message' => 'old password does not match'
            ], Response::HTTP_BAD_REQUEST);
        } else {
            return \response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function forgotPassword($email)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->email === $email) {
            $password = Str::random(10);
            // Truyền otp_code vào markdown email
            Mail::to($email)->send(new ForgotPasswordMail($password));
            $user->password = $password;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Mail sent successfully'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User is not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}



