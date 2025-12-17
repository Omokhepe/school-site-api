<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subjects')->truncate();

        $subjects = [
            // CRECHE (if your school teaches basic literacy & numeric skills)
            ['name' => 'Basic Literacy', 'level_group' => 'creche', 'color'=> '#FF6B6B'],
            ['name' => 'Basic Numeracy', 'level_group' => 'creche', 'color'=> '#4ECDC4'],
            ['name' => 'Health Habits', 'level_group' => 'creche', 'color'=> '#1A535C'],
            ['name' => 'Rhymes & Songs', 'level_group' => 'creche', 'color'=> '#FFE66D'],

            // PRIMARY
            ['name' => 'English', 'level_group' => 'primary', 'color'=> '#6A0572'],
            ['name' => 'Maths', 'level_group' => 'primary', 'color'=>'#FF7F50'],
            ['name' => 'Basic Sci', 'level_group' => 'primary', 'color'=>'#45B7D1'],
            ['name' => 'Social Studies', 'level_group' => 'primary', 'color'=>'#2D4059'],
            ['name' => 'Civic Edu', 'level_group' => 'primary', 'color'=>'#EA5455'],
            ['name' => 'Verbal Reasoning', 'level_group' => 'primary', 'color'=>'#F9D56E'],
            ['name' => 'Quantitative Reasoning', 'level_group' => 'primary', 'color'=>'#5C7AEA'],
            ['name' => 'Computer Studies', 'level_group' => 'primary', 'color'=>'#8D8741'],
            ['name' => 'Health Edu', 'level_group' => 'primary', 'color'=>'#CBF3F0'],
            ['name' => 'Agric Sci', 'level_group' => 'primary', 'color'=>'#FF9F1C'],
            ['name' => 'Creative Arts', 'level_group' => 'primary', 'color'=>'#2EC4B6'],

            // JSS (Junior Secondary)
            ['name' => 'English Language', 'level_group' => 'jss', 'color'=> '#6A0572'],
            ['name' => 'Maths', 'level_group' => 'jss', 'color'=>'#FF7F50'],
            ['name' => 'Basic Tech', 'level_group' => 'jss', 'color'=>'#45B7D1'],
            ['name' => 'Business Studies', 'level_group' => 'jss', 'color'=>'#CBF3F0'],
            ['name' => 'Civic Edu', 'level_group' => 'jss', 'color'=>'#EA5455'],
            ['name' => 'Social Studies', 'level_group' => 'jss', 'color'=>'#2D4059'],
            ['name' => 'Computer Studies', 'level_group' => 'jss', 'color'=>'#8D8741'],
            ['name' => 'Home Economics', 'level_group' => 'jss', 'color'=>'#7A9E9F'],
            ['name' => 'Agric Sci', 'level_group' => 'jss', 'color'=>'#FF9F1C'],
            ['name' => 'CRS', 'level_group' => 'jss', 'color'=>'#A53860'],
            ['name' => 'Fine Art', 'level_group' => 'jss', 'color'=>'#E8A598'],

            // SS (Senior Secondary)
            ['name' => 'English Language', 'level_group' => 'ss', 'color'=> '#6A0572'],
            ['name' => 'Maths', 'level_group' => 'ss', 'color'=>'#FF7F50'],
            ['name' => 'Biology', 'level_group' => 'ss', 'color'=>'#4A7B9D'],
            ['name' => 'Physics', 'level_group' => 'ss', 'color'=>'#82C0CC'],
            ['name' => 'Chemistry', 'level_group' => 'ss', 'color'=>'#C38D9E'],
            ['name' => 'Government', 'level_group' => 'ss', 'color'=>'#6F1E51'],
            ['name' => 'Economics', 'level_group' => 'ss', 'color'=>'#1289A7'],
            ['name' => 'Literature in English', 'level_group' => 'ss', 'color'=>'#B53471'],
            ['name' => 'Geography', 'level_group' => 'ss', 'color'=>'#ED4C67'],
            ['name' => 'Commerce', 'level_group' => 'ss', 'color'=>'#009432'],
            ['name' => 'Computer Science', 'level_group' => 'ss', 'color'=>'#5758BB'],
            ['name' => 'Further Maths', 'level_group' => 'ss', 'color'=>'#EE5A24'],
        ];



       DB::table('subjects')->insert($subjects);
    }
}