<?php

use Illuminate\Database\Seeder;

class DbSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('db_skills')->insert([
            'Name'  =>  'Oracle',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'MS SQL',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'MDB',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'Postgre',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'MySQL',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'SqlLite',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'XML',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'Linq',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'MongoDB',

        ]);
        DB::table('db_skills')->insert([
            'Name'  =>  'Hadoop',

        ]);
    }
}
