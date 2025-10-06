<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run role and admin seeders
    $this->call([
        RolesSeeder::class,
        AdminSeeder::class,
    ]);

    // Optional: create a sample user
    // User::factory()->create([
    //     'first_name' => 'Test',
    //     'last_name' => 'User',
    //     'email' => 'test@example.com',
    //     'country' => 'United States',
    //     'phone' => '+1 555-0100',
    //     'company' => 'Example Co.',
    // ]);
    }
}
