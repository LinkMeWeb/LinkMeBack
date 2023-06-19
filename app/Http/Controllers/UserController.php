<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewUserRequest;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use function auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::select('id','nickname','photo_path')->get();
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewUserRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nickname' => $request->nickname,
            'password' => bcrypt($request->password),
            'photo_path' => '',
            'about' => $request->about
        ]);
        if ($request->photo_path == 'default') {
            $user->photo_path = Storage::url('profile/Default.png');
        }else {
            $imageName = $user->id.'-profile.png';
            $photoToStorage = base64_decode($request->photo_path);
            Storage::disk('public')->put('profile/' . $imageName, $photoToStorage);
            $user->photo_path = Storage::url('profile/' .$imageName);
        }
        $user->save();
        return response()->json($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $photos = Photo::where('user_id', $id)->get();
        $user->setRelation('photos', $photos);

        return $user;
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);
        $user->delete();
        return response()->json(null, 204);
    }

    public function patch(Request $request)
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'El usuario no existe.'], 404);
        }
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('nickname')) {
            $user->nickname = $request->nickname;
        }
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        if ($request->has('photo_path')) {
            $imageName = $userId.'-profile.png';
            $photoToStorage = base64_decode($request->photo_path);
            Storage::disk('public')->put('profile/' . $imageName, $photoToStorage);
            $user->photo_path = Storage::url('profile/' .$imageName);
        }
        if ($request->has('about')) {
            $user->about = $request->about;
        }
        $user->save();
        return response()->json($user);
    }

    public function getAllByParams(Request $request)
    {
        $users = User::where('id', '<>', auth()->user()->id)
            ->inRandomOrder()
            ->limit($request->limit)
            ->get();
        return $users;
    }

    public function findByName(string $nickname)
    {
        $user = User::where('nickname', $nickname)->first();
        return $user;
    }

    public function emailExists(string $email)
    {
        $user = User::where('email', $email)->first();
        return response()->json((bool) $user);
    }

    public function nicknameExists(string $nickname)
    {
        $user = User::where('nickname', $nickname)->first();
        return response()->json((bool) $user);
    }

    public function getUserImages(string $nickname)
    {
        $user = User::where('nickname', $nickname)->first();
        if ($user) {
            return Photo::select('id','photo')->where('user_id',$user->id)->orderBy('created_at', 'desc')->get();
        }
        return response()->json('Error');
    }

    function searchUser(String $name) {
        if ($name) {
            $users = User::where('nickname', 'like', $name.'%')->select('id','nickname','photo_path')->limit(5)->get();
            if ($users) {
                foreach ($users as $user) {
                    $path = public_path('storage').'/profile/' . $user->photo_path;
                    if (file_exists($path)) {
                        $user->photo_path = base64_encode(file_get_contents($path));
                    }
                }
                return $users;
            }
            return [];
        }
    }

    function follow(Request $request) {
        $user = User::findOrFail(auth()->user()->id);
        $followUser = User::findOrFail($request->id);

        $isFollowing = $user->follows()->where('follower_id', $followUser->id)->exists();

        if (!$isFollowing) {
            $user->follows()->attach($followUser->id);
            return true;
        }
        $user->follows()->detach($followUser->id);
        return false;
    }

    function getFollows(int $id) {
        $followingCount = User::findOrFail($id)->follows()->count();
        return response()->json(['follows' => $followingCount]);
    }

    function getFollowers(int $id) {
        $followersCount = User::whereHas('follows', function ($query) use ($id) {
            $query->where('follower_id', $id);
        })->count();

        return response()->json(['followers' => $followersCount]);
    }

    function checkFollowing(int $id) {
        $user = User::findOrFail(auth()->user()->id);
        $followUser = User::findOrFail($id);

        return $user->follows()->where('follower_id', $followUser->id)->exists();
    }

    function getLikedPhotos() {
        $user = User::find(auth()->user()->id);
        return $user->likes()->orderBy('created_at', 'desc')->get();
    }

}
