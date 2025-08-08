<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a user with default role
        User::factory()->create([
            'name' => 'John Doe',
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);
    }   
}
