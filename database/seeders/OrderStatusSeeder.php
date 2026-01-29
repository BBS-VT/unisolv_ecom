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
                'colour'      => 'info',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '2',
                'name'       => 'Downloaded',
                'colour'      => 'primary',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '3',
                'name'       => 'Delivery',
                'colour'      => 'warning',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '4',
                'name'       => 'Invoiced',
                'colour'      => 'dark',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '5',
                'name'       => 'On Hold',
                'colour'      => 'warning',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '6',
                'name'       => 'Ready for Collection',
                'colour'      => 'success',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '7',
                'name'       => 'Ready for Delivery',
                'colour'      => 'success',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '8',
                'name'       => 'Completed',
                'colour'      => 'secondary',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],
            [
                'id'         => '9',
                'name'       => 'Cancelled',
                'colour'      => 'danger',
                'created_at' => '2021-04-18 06:10:05',
                'updated_at' => '2021-04-18 06:10:05',
            ],

        ];

        OrderStatus::insert($orderstatus);
    }
}
