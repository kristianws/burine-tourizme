<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'review_id' =>
                'required|exists:reviews,id',

            'parent_id' =>
                'nullable|exists:comments,id',

            'description' =>
                'required|string'
        ]);

        $comment = Comment::create([
            'review_id' =>
                $validated['review_id'],

            'user_id' =>
                $request->user()->id,

            'parent_id' =>
                $validated['parent_id'] ?? null,

            'description' =>
                $validated['description']
        ]);

        return response()->json([
            'message' => 'Komentar berhasil dibuat',
            'data' => $comment
        ], 201);
    }

    public function reviewComments($reviewId)
    {
        $comments = Comment::with([
            'user',
            'replies.user'
        ])
        ->where(
            'review_id',
            $reviewId
        )
        ->whereNull('parent_id')
        ->latest()
        ->get();

        return response()->json(
            $comments
        );
    }
}