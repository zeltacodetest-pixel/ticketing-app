<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔹 Developer 1
        $dev1 = User::firstOrCreate(
            ['email' => 'dev1@ticket.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'country' => 'United States',
                'phone' => '+1 555-1111',
                'company' => 'Ticket HQ',
                'password' => Hash::make('00000000'),
            ]
        );

        // 🔹 Developer 2
        $dev2 = User::firstOrCreate(
            ['email' => 'dev2@ticket.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'country' => 'United States',
                'phone' => '+1 555-2222',
                'company' => 'Ticket HQ',
                'password' => Hash::make('00000000'),
            ]
        );

        // Assign roles
        if (! $dev1->hasRole('Developer')) {
            $dev1->assignRole('Developer');
        }

        if (! $dev2->hasRole('Developer')) {
            $dev2->assignRole('Developer');
        }
    }
}
