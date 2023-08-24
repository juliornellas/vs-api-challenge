<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Adding an admin user
        $user = \App\Models\User::factory()
            ->count(1)
            ->create([
                'email' => 'admin@admin.com',
                'password' => \Hash::make('admin'),
            ]);

        // Adding a manager user
        $user2 = \App\Models\User::factory()
        ->count(1)
        ->create([
            'email' => 'manager@admin.com',
            'password' => \Hash::make('manager'),
        ]);

        $this->call(FavoriteSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(UserSeeder::class);
    }
}