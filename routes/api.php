<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [API\AuthController::class, 'getMe']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [API\AuthController::class, 'register']);
    Route::post('/login', [API\AuthController::class, 'login']);
    Route::get('/verify/{email}', [API\AuthController::class, 'sendVerifyMail']);
    Route::post('/checkverify/{otp_code}',[API\AuthController::class,'verifiOTP']);
    Route::post('/change-password',[API\AuthController::class,'changePassword']);
    Route::post('forgotpassword/{email}',[API\AuthController::class,'forgotPassword']);
});
Route::get('/me', [API\AuthController::class, 'getMe']);
Route::get('/logout',[API\AuthController::class,'logout']);
Route::get('/role', function (){
    $roleUser = Role::firstOrCreate(['name' => 'user']);
    $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
    $adminUser = User::where('name', 'admin')->first();
    $adminUser->assignRole('admin');
});



Route::middleware(['custom_auth'])->group(function () {
    Route::prefix('post')->group(function (){
        Route::get('/index/{id}', [API\PostController::class, 'index']);
        Route::post('/update/{id}', [API\PostController::class, 'update']);
        Route::delete('/delete/{id}',[API\PostController::class,'destroy']);
        Route::post('/store',[API\PostController::class,'store']);
        Route::get('/profile-post', [API\PostController::class, 'getAllMyPost']);
        Route::get('/all-post',[API\PostController::class,'getAllPost']);
    });
    Route::prefix('admin')->group(function (){
        Route::get('/post-no-access',[API\AdminController::class,'getAllPostNoAccess']);
        Route::get('/post-access',[API\AdminController::class,'getAllPostAccess']);
        Route::post('/post-reviewer/{id}',[API\AdminController::class, 'PostReviewer']);
    });

    Route::get('/tag/{tag}',[API\TagController::class,'search']);

    Route::post('/like/{id}',[API\LikeController::class,'likePost']);

    Route::get('/notification',[API\Notifications::class,'index']);

    Route::post('/comment/{id}',[API\CommentController::class,'store']);

    Route::post('/like-comment/{id}', [API\LikeController::class, 'likeComment']);

    Route::get('/comment/{id}',[API\CommentController::class,'index']);

    Route::post('/notification-seen/{id}',[API\Notifications::class,'markAsSeen']);

    Route::post('sendRequest-friend/{id}',[API\FriendController::class,'sendRequest']);

    Route::delete('/CancelRequest-friend/{id}',[API\FriendController::class,'CancelRequest']);

});

Route::post('/role',function (){
    $role = \Spatie\Permission\Models\Role::create(['name' => 'user']);
});
