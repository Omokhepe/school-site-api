<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;

class UserIdGeneratorService
{
public function generate(string $role): string
{
    $prefix = $role === 'student' ? 'STU' : 'TCH';
    $year = date('Y');

    //Get current last_number
    $counter = DB::table('id_counters')->where('role', $role)->first();

    if (!$counter) {
        DB::table('id_counters')->insert([
            'role' => $role,
            'last_number' => 0,
        ]);
        $counter = (object)[
            'last_number' => 0,
        ];
    }
    //increment
    $nextNumber = $counter->last_number + 1;

    //update DB
    DB::table('id_counters')->where('role', $role)->update(['last_number' => $nextNumber]);

    //format: STU-2025-000003
    return sprintf('%s-%s-%06d', $prefix,$year, $nextNumber);
}
}
