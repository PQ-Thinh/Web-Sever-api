<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 1 User Admin (Quản trị viên)
        User::create([
            'name' => 'Admin Tối Cao',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        // Tạo 1 User mẫu (Khách hàng)
        User::create([
            'name' => 'Khách hàng VIP',
            'email' => 'khachhang@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'customer',
        ]);

        // Tạo 2 Danh mục
        $catDienThoai = Category::create([
            'name' => 'Điện thoại thông minh',
            'description' => 'Các dòng smartphone mới nhất'
        ]);

        $catLaptop = Category::create([
            'name' => 'Máy tính xách tay',
            'description' => 'Laptop văn phòng và gaming'
        ]);

        // Tạo Sản phẩm
        Product::create([
            'name' => 'iPhone 15 Pro Max',
            'price' => 30000000,
            'stock' => 10,
            'category_id' => $catDienThoai->id
        ]);
        Product::create([
            'name' => 'Samsung Galaxy S24 Ultra',
            'price' => 28000000,
            'stock' => 15,
            'category_id' => $catDienThoai->id
        ]);
        Product::create([
            'name' => 'MacBook Air M3',
            'price' => 25000000,
            'stock' => 5,
            'category_id' => $catLaptop->id
        ]);
        Product::create([
            'name' => 'Dell XPS 13',
            'price' => 27000000,
            'stock' => 8,
            'category_id' => $catLaptop->id
        ]);

        // Tạo thêm 50 sản phẩm ngẫu nhiên để test phân trang
        $products = Product::factory(50)->create();

        // Tạo Đánh giá mẫu cho các sản phẩm
        foreach ($products->random(10) as $product) {
            \App\Models\Review::create([
                'user_id' => 2, // Khách hàng
                'product_id' => $product->id,
                'rating' => rand(3, 5),
                'comment' => 'Sản phẩm này rất tốt, đáng đồng tiền bát gạo!'
            ]);
        }
    }
}
