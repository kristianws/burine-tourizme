<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\ApiResponse;
use App\Http\Requests\ReplyRequest;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
  public function reviewByDestinationId(int $destinationId): JsonResponse
  {
    $reviews = Review::with('user:id,name,profile_picture')
      ->where('destination_id', $destinationId)
      ->get();

    $reviews = ReviewResource::collection($reviews);

    return $this->successResponse($reviews);
  }

  public function store(ReviewRequest $request)
  {
    $validated = $request->validated();

    $review = Review::create($validated);
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
}
