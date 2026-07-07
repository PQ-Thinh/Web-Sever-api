<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)->with('user:id,name')->latest()->get();
        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        // Kiểm tra khách hàng đã từng mua sản phẩm này chưa
        $hasBought = Order::where('user_id', $request->user()->id)
            ->whereHas('items', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })->exists();

        if (!$hasBought) {
            return response()->json(['message' => 'Bạn phải mua sản phẩm này mới được đánh giá'], 403);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json($review, 201);
    }
}
