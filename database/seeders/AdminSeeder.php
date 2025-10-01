<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
  {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ticket.com'],
            [
                'name' => 'Super Admin',
                'country' => 'United States',
                'phone' => '+1 555-0000',
                'company' => 'Ticket HQ',
                'password' => Hash::make('00000000'),
            ]
        );

        // make sure role exists
        if (! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }
    }
}
