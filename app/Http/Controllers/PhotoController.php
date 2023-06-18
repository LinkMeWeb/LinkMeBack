<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPhotoRequest;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function auth;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Photo::orderBy('created_at', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewPhotoRequest $request)
    {
        $userId = auth()->user()->id;
        $numPhotos = Photo::where('user_id', $userId)->count();
        $imageName = $userId.'-'.$numPhotos.'.png';
        $photoToStorage = base64_decode($request->photo);
        Storage::disk('public')->put('images/' . $imageName, $photoToStorage);
        $photo = new Photo();
        $photo->title = $request->title;
        $photo->description = $request->description;
        $photo->likes = 0;
        $photo->user_id = $userId;
        $photo->photo = Storage::url('images/' .$imageName);
        $photo->save();
        return response()->json($photo);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Photo::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $photo = Photo::find($id);
        $photo->title = $request->title;
        $photo->description = $request->description;
        $photo->likes = $request->likes;
        $photo->save();
        return response()->json($photo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $photo = Photo::find($id);
        $photo->delete();
        return response()->json(null, 200);
    }


    public function likeRequest(string $id)
    {
        $photo = Photo::findOrFail($id);
        $user = auth()->user();
        $userPhoto = $user->likes()
            ->wherePivot('photo_id', $photo->id)
            ->first();
        if ($userPhoto) {
            return $this->doDislike($photo, $user->id);
        }
        return $this->doLike($photo, $user->id);
    }

    public function liked(string $id)
    {
        $photo = Photo::findOrFail($id);
        $user = auth()->user();
        return (bool)$user->likes()
            ->wherePivot('photo_id', $photo->id)
            ->first();
    }

    private function doLike(Photo $photo, string $id)
    {
        $photo->likes()->attach($id);
        $photo->likes++;
        $photo->save();
        return true;
    }

    private function doDislike(Photo $photo, string $id)
    {
        $photo->likes()->detach($id);
        $photo->likes--;
        $photo->save();
        return false;
    }

}
