<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Photo;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function getAllPhotoComments(int $photo) {
        return Comment::where('photo_id', $photo)->get();
    }

    public function store(Request $request, Photo $photo)
    {
        $validatedData = $request->validate([
            'content' => 'required|max:255',
        ]);

        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->user_id = auth()->user()->id;
        $photo->comments()->save($comment);

        return response()->json(['message' => 'Comment created successfully']);
    }
}
