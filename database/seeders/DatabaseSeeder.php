<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Image;
use App\Models\Product;
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
            ->hasVariants(5)
            ->has(
                Image::factory(3)
                    ->sequence(
                        fn(Sequence $sequence) => [
                            'featured' => $sequence->index === 0
                        ]))
            ->create();
    }
}
