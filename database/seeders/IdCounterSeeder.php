<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdCounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('id_counters')->insert([
            ['role'=>'student','last_number'=>0],
            ['role'=>'teacher','last_number'=>0],
        ]);
    }
}
