<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 100, 2000),
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => 'productos/GEXwLAAnlE6qmI6L4WtQhEEbUnPkgukeL25KVW1n.jpg',
            'brand_id' => $this->faker->randomElement([3, 4]),
            'category_id' => $this->faker->randomElement([2, 5, 6, 7, 8]),
            'user_id' => 2,
        ];
    }
}

