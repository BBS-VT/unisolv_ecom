<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'FullName'       => 'System User',
                'PreferredName'  => 'System',
                'email'          => 'support@unisolv.co.za',
                'password'       => '$2y$10$kztlYEbwoRzWeMSO86DSCuNe3iJlY8T127Zkl3uGJXf1Y9TuUfSU.',
                'remember_token' => null,
                'created_at'     => '2021-03-02 06:09:29',
                'updated_at'     => '2021-03-02 06:09:29',
            ],
        ];

        User::insert($users);
    }
}
