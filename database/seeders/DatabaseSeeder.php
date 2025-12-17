<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([SchoolClassSeeder::class, AdminSeeder::class, IdCounterSeeder::class, SubjectSeeder::class]);
        User::factory()->create([
            'first_name'=>'Test',
            'last_name'=>'User',
            'username'=>'test',
            'user_id'=>'STU-2025-000001',
            'gender'=>"male",
            'phone_number'=>'09090900982',
            'address'=>'Sen Gil Puyat, Makati',
            'date_of_birth'=>'2025-10-04',
            'state_of_origin'=>'Ondo',
            'parent_first_name' => 'Tester',
            'parent_last_name' => 'Mom',
            'parent_phone' => '09078896509',
            'parent_address' => 'Sen Gil Puyat, Makati',
        ]);
    }
}