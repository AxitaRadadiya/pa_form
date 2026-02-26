<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\FoodSeeder;
use Database\Seeders\ChapterSeeder;
use Database\Seeders\RelationSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Existing test user (create if not exists)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Superadmin account for local/dev use (ensure role = admin)
        if (!User::where('email', 'superadmin@gmail.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('123456'),
                'role' => 'admin',
            ]);
        } else {
            // If user exists, ensure role is admin
            $existing = User::where('email', 'superadmin@gmail.com')->first();
            if ($existing && $existing->role !== 'admin') {
                $existing->role = 'admin';
                $existing->save();
            }
        }

        // Seed foods
        $this->call(FoodSeeder::class);
        // Seed chapters
        $this->call(ChapterSeeder::class);
        // Seed relations
        $this->call(RelationSeeder::class);
    }
}
