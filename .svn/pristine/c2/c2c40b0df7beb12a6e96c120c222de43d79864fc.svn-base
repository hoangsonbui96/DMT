<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'Name'  =>  'role.view',
            'Action'    =>  'view',


        ]);

        DB::table('roles')->insert([
            'Name'  =>  'role.create',
            'Action'    =>  'create',


        ]);
        DB::table('roles')->insert([
            'Name'  =>  'role.update',
            'Action'    =>  'update',


        ]);
        DB::table('roles')->insert([
            'Name'  =>  'role.delete',
            'Action'    =>  'delete',


        ]);
        DB::table('roles')->insert([
            'Name'  =>  'role.admin',
            'Action'    =>  'admin',


        ]);
    }
}
