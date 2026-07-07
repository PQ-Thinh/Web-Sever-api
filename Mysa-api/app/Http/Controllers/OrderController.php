<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'user']);
        
        // Nếu không phải Admin, chỉ được xem đơn của chính mình
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'phone' => 'required|string|max:15'
        ]);

        $user = $request->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng của bạn đang trống'], 400);
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => 'pending', // pending, shipped, delivered
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone
            ]);

            $totalAmount = 0;

            foreach ($cart->items as $item) {
                $product = $item->product;

                if ($product->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ số lượng trong kho.");
                }
                
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price
                ]);

                $totalAmount += $product->price * $item->quantity;
                $product->decrement('stock', $item->quantity);
            }

            // Áp dụng Coupon nếu có
            $discountAmount = 0;
            if ($request->has('coupon_code')) {
                $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && (!$coupon->valid_until || now()->lessThanOrEqualTo($coupon->valid_until)) && (!$coupon->usage_limit || $coupon->used_count < $coupon->usage_limit)) {
                    
                    if ($coupon->discount_type == 'percent') {
                        $discountAmount = ($totalAmount * $coupon->discount_amount) / 100;
                    } else {
                        $discountAmount = $coupon->discount_amount;
                    }
                    
                    $coupon->increment('used_count');
                    $order->update([
                        'coupon_id' => $coupon->id,
                        'discount_amount' => $discountAmount
                    ]);
                }
            }

            $order->update(['total_amount' => max(0, $totalAmount - $discountAmount)]);

            $cart->items()->delete();

            DB::commit();

            return response()->json(['message' => 'Đặt hàng thành công', 'order' => $order->load('items')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,shipped,delivered']);
        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'Đã cập nhật trạng thái đơn hàng', 'order' => $order]);
    }
}
