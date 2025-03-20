<?php

namespace Database\Factories;

use App\Models\AttributeVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VariantAttribute>
 */
class AttributeVariantFactory extends Factory
{
    protected $model = AttributeVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {        
        // Definir posibles pares de clave-valor
        $attributes = [
            ['key' => 'color', 'value' => $this->faker->randomElement(['red', 'blue', 'green', 'black'])],
            ['key' => 'size', 'value' => $this->faker->randomElement(['S', 'M', 'L', 'XL'])],
            ['key' => 'material', 'value' => $this->faker->randomElement(['cotton', 'polyester', 'wool'])],
        ];

        // Seleccionar un par clave-valor aleatorio
        return $this->faker->randomElement($attributes);
    }
}
