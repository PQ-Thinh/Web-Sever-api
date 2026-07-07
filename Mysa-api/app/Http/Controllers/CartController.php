<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Lấy giỏ hàng của user
    public function index(Request $request)
    {
        $cart = Cart::with('items.product')->firstOrCreate(['user_id' => $request->user()->id]);
        return response()->json($cart);
    }

    // Thêm hoặc cập nhật sản phẩm trong giỏ
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['message' => 'Đã cập nhật giỏ hàng', 'cart' => $cart->load('items.product')]);
    }

    // Xóa sản phẩm khỏi giỏ
    public function remove(Request $request, $itemId)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();
        if ($cart) {
            CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
        }

        return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ']);
    }
}
