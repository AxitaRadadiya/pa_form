<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('foods')->insert([
            ['name' => 'Jain/Swaminarayan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Regular', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
