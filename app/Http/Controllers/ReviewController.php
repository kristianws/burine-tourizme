<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewReply;
use App\Models\Destination;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReviewReplyResource;
use App\ApiResponse;
use App\Http\Requests\ReplyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function show(Destination $destination): JsonResponse
    {
        $reviews = Review::where('destination_id', $destination->id)
            ->with(['user', 'replies' => function ($q) {
                $q->with('user')->whereNull('parent_id')
                  ->with(['children' => function ($q2) {
                      $q2->with('user')->with(['children' => function ($q3) {
                          $q3->with('user');
                      }]);
                  }]);
            }])
            ->get();

        $reviews = ReviewResource::collection($reviews);

        return $this->successResponse($reviews);
    }



    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();

        $review = Review::create([
            'user_id' => $request->user()->id,
            'destination_id' => $validated['destination_id'],
            'rating' => $validated['rating'],
            'description' => $validated['description'] ?? null,
        ]);
        $review->load('user');
        $review = new ReviewResource($review);

        return $this->successResponse($review, 'Review berhasil ditambahkan');
    }

    public function update(UpdateReviewRequest $request)
    {
        $validated = $request->validated();

        $review = Review::findOrFail($validated['review_id']);
        $review->rating = $validated['rating'];
        $review->description = $validated['description'] ?? $review->description;
        $review->save();
        $review = new ReviewResource($review);

        return $this->successResponse($review, 'Review berhasil diperbarui');
    }

    public function replyReview(Review $review, ReplyRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $review->owner_reply = $validatedData['owner_reply'];
        $review->save();
        $review = new ReviewResource($review);

        return $this->successResponse($review);
    }

    /**
     * Store a reply to a review (or to another reply)
     */
    public function storeReply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'parent_id' => 'nullable|exists:review_replies,id',
            'content' => 'required|string|max:1000',
        ]);

        $reply = ReviewReply::create([
            'review_id' => $validated['review_id'],
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        $reply->load('user');

        return $this->successResponse(
            new ReviewReplyResource($reply),
            'Balasan berhasil ditambahkan',
            201
        );
    }

    /**
     * Get all replies for a review (threaded)
     */
    public function getReplies(Review $review): JsonResponse
    {
        $replies = ReviewReply::where('review_id', $review->id)
            ->whereNull('parent_id')
            ->with(['user', 'children' => function ($q) {
                $q->with('user')->with(['children' => function ($q2) {
                    $q2->with('user');
                }]);
            }])
            ->latest()
            ->get();

        return $this->successResponse(
            ReviewReplyResource::collection($replies),
            'Balasan berhasil diambil'
        );
    }
}
