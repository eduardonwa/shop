<?php

namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Models\ProductVariant;
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
        $attribute = Attribute::factory()->create([
            'key' => 'attr_' . Str::random(8) // Clave aleatoria única
        ]);

        return [
            'product_variant_id' => ProductVariant::factory(),
            'attribute_id' => $attribute->id,
            'value' => $this->faker->word, // Valor simple aleatorio
        ];
    }
}
