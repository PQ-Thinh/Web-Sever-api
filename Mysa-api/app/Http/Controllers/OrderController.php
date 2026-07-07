<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        // Lấy danh sách đơn hàng kèm chi tiết sản phẩm
        return response()->json(Order::with('items.product')->get());
    }

    public function store(Request $request)
    {
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
                'status' => 'pending' // pending, completed, cancelled
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

            $order->update(['total_amount' => $totalAmount]);

            // Xóa sạch giỏ hàng sau khi đặt thành công
            $cart->items()->delete();

            DB::commit();

            return response()->json(['message' => 'Đặt hàng thành công', 'order' => $order->load('items')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
