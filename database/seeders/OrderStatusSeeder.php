<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orderstatus = [
            [
                'id'         => '1',
                'name'       => 'New',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '2',
                'name'       => 'Downloaded',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '3',
                'name'       => 'Delivery',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '4',
                'name'       => 'Invoiced',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],

        ];

        OrderStatus::insert($orderstatus);
    }
}
