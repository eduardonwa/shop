<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Image;
use App\Models\Product;
use App\Models\AttributeVariant;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        
        $adminRole = Role::where('name', 'admin')->first();

        $adminUser = User::factory()->create([
            'name' => 'eduardo',
            'email' => 'eduardo@hotmail.com',
            'password' => 'password',
        ]);

        $adminUser->assignRole($adminRole);

        Product::factory(4)
            ->hasVariants(5, function (array $attributes, Product $product) {
                return ['product_id' => $product->id];
            })
            ->has(
                Image::factory(3)
                    ->sequence(
                        fn(Sequence $sequence) => [
                            'featured' => $sequence->index === 0
                        ]))
            ->create()
            ->each(function ($product) {
                // para cada variante del producto, crear 2 atributos
                $product->variants->each(function ($variant) {
                    AttributeVariant::factory(3)->create([
                        'product_variant_id' => $variant->id,
                    ]);
                });
            });
    }
}
