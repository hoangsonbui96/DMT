<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoleUserGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_user_groups')->insert([
            'id' => 1,
            'name' => 'Root',
            'alias'  =>  'root',
            'role'  =>  1,
            'created_at'    =>  Carbon::now(),
            'updated_at'    =>  Carbon::now(),
        ]);

        DB::table('role_user_groups')->insert([
            'id' => 2,
            'name' => 'Admin',
            'alias'  =>  'admin',
            'role'  =>  2,
            'created_at'    =>  Carbon::now(),
            'updated_at'    =>  Carbon::now(),
        ]);

        DB::table('role_user_groups')->insert([
            'id' => 3,
            'name' => 'User',
            'alias'  =>  'user',
            'role'  =>  3,
            'created_at'    =>  Carbon::now(),
            'updated_at'    =>  Carbon::now(),
        ]);
    }
}
