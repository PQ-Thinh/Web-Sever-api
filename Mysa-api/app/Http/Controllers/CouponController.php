<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return response()->json(Coupon::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:coupons',
            'discount_amount' => 'required|integer|min:1',
            'discount_type' => 'required|in:percent,fixed',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date'
        ]);

        return response()->json(Coupon::create($data), 201);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code' => 'string|unique:coupons,code,'.$coupon->id,
            'discount_amount' => 'integer|min:1',
            'discount_type' => 'in:percent,fixed',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date'
        ]);

        $coupon->update($data);
        return response()->json($coupon);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $coupon = Coupon::where('code', $request->code)->first();
        
        if (!$coupon) return response()->json(['message' => 'Mã không hợp lệ'], 404);
        
        if ($coupon->valid_until && now()->greaterThan($coupon->valid_until)) {
            return response()->json(['message' => 'Mã đã hết hạn'], 400);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['message' => 'Mã đã hết lượt sử dụng'], 400);
        }

        return response()->json(['message' => 'Áp dụng mã thành công', 'coupon' => $coupon]);
    }
}
