<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public static function run(): void
    {
        User::factory()->create([
            'name' => 'Root',
            'email' => 'root@root.com',
            'password' => bcrypt('roottoor'),
            'role' => 'root',
            'email_verified_at' => now(),
        ]);
    }
}
