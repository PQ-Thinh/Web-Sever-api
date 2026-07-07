<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->words(3, true)),
            'price' => $this->faker->numberBetween(100000, 50000000),
            'stock' => $this->faker->numberBetween(0, 100),
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,
            'image_url' => 'https://picsum.photos/400/400?random=' . rand(1, 1000),
            'description' => $this->faker->paragraph(3),
        ];
    }
}
