<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::get('/user',function () {
    return abort(404);
});

Route::apiResource('users',\App\Http\Controllers\API\UserController::class)->only('index');
Route::apiResource('user',\App\Http\Controllers\API\UserController::class)->except('index');

Route::apiResource('photos', \App\Http\Controllers\API\PhotoController::class)->only('index');
Route::apiResource('photo', \App\Http\Controllers\API\PhotoController::class)->except('index');*/





Route::post('user-create', [\App\Http\Controllers\UserController::class, 'store']);

Route::post('login',[\App\Http\Controllers\AuthController::class, 'login']);

Route::get('/checkUser/{nickname}', [\App\Http\Controllers\UserController::class, 'nicknameExists']);

Route::get('/checkEmail/{email}', [\App\Http\Controllers\UserController::class, 'emailExists']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ["auth:sanctum"]], function () {
    Route::get('user/{id}', [\App\Http\Controllers\UserController::class, 'show']);
    Route::get('user-profile', [\App\Http\Controllers\AuthController::class, 'userProfile']);
    Route::apiResource('photo', \App\Http\Controllers\PhotoController::class);
    Route::get('user/profile/image/{id}', [\App\Http\Controllers\UserController::class, 'getProfilePicture']);
    Route::get('user/search/users/{user}', [\App\Http\Controllers\UserController::class, 'searchUser']);
    Route::post('/users/params', [\App\Http\Controllers\UserController::class, 'getAllByParams']);
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index']);
    Route::delete('user', [\App\Http\Controllers\UserController::class, 'destroy']);
    Route::get('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::patch('user/update-profile', [\App\Http\Controllers\UserController::class, 'patch']);
    Route::get('/user/get-user/{nickname}',[\App\Http\Controllers\UserController::class, 'findByName']);
    Route::get('/user/like/{id}', [\App\Http\Controllers\PhotoController::class, 'likeRequest']);
    Route::get('/user/liked/{id}', [\App\Http\Controllers\PhotoController::class, 'liked']);
    Route::get('/user/photos/likedPhotos', [\App\Http\Controllers\UserController::class, 'getLikedPhotos']);
    Route::get('/user/images/{id}', [\App\Http\Controllers\UserController::class, 'getUserImages']);
    Route::post('/user/follow', [\App\Http\Controllers\UserController::class, 'follow']);
    Route::get('/user/follows/{id}', [\App\Http\Controllers\UserController::class, 'getFollows']);
    Route::get('/user/followers/{id}', [\App\Http\Controllers\UserController::class, 'getFollowers']);
    Route::get('/user/checkFollowing/{id}', [\App\Http\Controllers\UserController::class, 'checkFollowing']);
    Route::post('/photos/{photo}/comments', [\App\Http\Controllers\CommentController::class, 'store']);
    Route::get('/photos/{photo}/comments', [\App\Http\Controllers\CommentController::class, 'getAllPhotoComments']);
});

