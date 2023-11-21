<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => env('MASTER_USER_NAME'),
            'email' => env('MASTER_USER_EMAIL'),
            'api_token' => hash('sha256', env('MASTER_USER_API_TOKEN')),
        ]);
    }
}
