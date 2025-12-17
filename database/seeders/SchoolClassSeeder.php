<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('school_classes')->insert([
            // Creche
            ['name' => 'Creche', 'level' => 'creche'],

            // Primary
            ['name' => 'Primary 1', 'level' => 'primary'],
            ['name' => 'Primary 2', 'level' => 'primary'],
            ['name' => 'Primary 3', 'level' => 'primary'],
            ['name' => 'Primary 4', 'level' => 'primary'],
            ['name' => 'Primary 5', 'level' => 'primary'],
            ['name' => 'Primary 6', 'level' => 'primary'],

            // Secondary
            ['name' => 'JSS 1', 'level' => 'jss'],
            ['name' => 'JSS 2', 'level' => 'jss'],
            ['name' => 'JSS 3', 'level' => 'jss'],
            ['name' => 'SS 1', 'level' => 'ss'],
            ['name' => 'SS 2', 'level' => 'ss'],
            ['name' => 'SS 3', 'level' => 'ss'],
        ]);
    }
}