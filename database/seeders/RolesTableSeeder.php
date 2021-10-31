<?php

namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id'         => 1,
                'title'      => 'Admin',
                'created_at' => '2021-03-02 06:09:29',
                'updated_at' => '2021-03-02 06:09:29',
            ],
            [
                'id'         => 2,
                'title'      => 'Office User',
                'created_at' => '2021-03-02 06:09:29',
                'updated_at' => '2021-03-02 06:09:29',
            ],
            [
                'id'        => 3,
                'title'     => 'Sales Rep',
                'created_at' => '2021-03-02 06:09:29',
                'updated_at' => '2021-03-02 06:09:29',
            ]
        ];

        Role::insert($roles);
    }
}
