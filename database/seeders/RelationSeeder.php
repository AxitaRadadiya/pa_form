<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelationSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('relations')->insert([
            ['name' => 'Members', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Wife', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Child', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Future Partner', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Family member', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'other', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
