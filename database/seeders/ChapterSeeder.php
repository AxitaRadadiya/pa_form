<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $chapters = [
            ['name' => 'KARNAVATI', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SABARMATI', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HERITAGE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'RIVERFRONT', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ISRO', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('chapters')->insert($chapters);
    }
}
