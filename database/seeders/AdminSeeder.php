<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            'first_name'=>'Admin',
            'last_name'=>'User',
            'username'=>'admin',
            'user_id'=>'TCH-2025-000001',
            'gender'=>"female",
            'password'=> Hash::make('Admin123'),
            'role'=>'admin',
            'must_change_password'=>false,
            'phone_number'=>'09090900982',
            'address'=>'Sen Gil Puyat, Makati',
            'date_of_birth'=>'2025-10-04',
            'state_of_origin'=>'Ondo',
            'highest_education'=>'University',
            'degree'=>'Masters',
            'course'=>'Software',
            'reference_name'=>'Omoh Gaha',
            'reference_phone'=>'09080903421',
        ]);
    }
}