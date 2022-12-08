<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id'    =>  220,
            'FullName' => 'Root',
            'username'  =>  'root',
            'email' => Str::random(10).'@gmail.com',
            'password' => bcrypt('123456'),
            'role_group' => 1,
            'isAdmin'   =>  1,
            'Active'    =>  1,
        ]);
        DB::table('users')->insert([
            'FullName' => 'Mr Test',
            'username'  =>  'admin',
            'email' => Str::random(10).'@gmail.com',
            'password' => bcrypt('123456'),
            'isAdmin'   =>  1,
            'role_group' => 2,
            'Active'    =>  1,
        ]);

        DB::table('users')->insert([
            'FullName' => 'Test User',
            'username'  =>  'testuser',
            'email' => Str::random(10).'@gmail.com',
            'password' => bcrypt('123456'),
            'role_group' => 3,
            'isAdmin'   =>  0,
            'Active'    =>  1,
        ]);
    }
}
