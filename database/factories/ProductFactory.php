<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseNames = ['Laravel Cap', 'Tshirt', 'Blanket', 'Sweater', 'Hoodie', 'Mug'];

        return [
            'name' => $this->faker->randomElement($baseNames),
            'description' => $this->faker->paragraph(2),
            'price' => $this->faker->numberBetween(5_00, 45_00),
            'published' => $this->faker->boolean,
            'total_product_stock' => $this->faker->numberBetween(0, 5),
        ];
    }
}
