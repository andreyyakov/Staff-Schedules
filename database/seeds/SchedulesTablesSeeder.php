<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shops')->insert([
            'id' => 1,
            'name' => 'FunHouse',
        ]);

        DB::table('staff')->insert([
            [
                'id' => 1,
                'first_name' => 'Black Widow',
                'surname' => '',
                'shop_id' => 1,
            ], [
                'id' => 2,
                'first_name' => 'Thor ',
                'surname' => '',
                'shop_id' => 1,
            ], [
                'id' => 3,
                'first_name' => 'Wolverine ',
                'surname' => '',
                'shop_id' => 1,
            ], [
                'id' => 4,
                'first_name' => 'Gamora',
                'surname' => '',
                'shop_id' => 1,
            ]
        ]);

        DB::table('rotas')->insert([
            [
                'id' => 1,
                'shop_id' => 1,
                'week_commence_date' => (new DateTime)->format('2018-11-19'),
            ]
        ]);

        DB::table('shifts')->insert([
            [
                'id' => 1,
                'rota_id' => 1,
                'staff_id' => 1,
                'start_time' => (new DateTime)->format('2018-11-19 08:00:00'),
                'end_time' => (new DateTime)->format('2018-11-19 18:00:00'),
            ], [
                'id' => 2,
                'rota_id' => 1,
                'staff_id' => 1,
                'start_time' => (new DateTime)->format('2018-11-20 08:00:00'),
                'end_time' => (new DateTime)->format('2018-11-20 14:00:00'),
            ], [
                'id' => 3,
                'rota_id' => 1,
                'staff_id' => 2,
                'start_time' => (new DateTime)->format('2018-11-20 14:00:00'),
                'end_time' => (new DateTime)->format('2018-11-20 18:00:00'),
            ], [
                'id' => 4,
                'rota_id' => 1,
                'staff_id' => 3,
                'start_time' => (new DateTime)->format('2018-11-21 08:00:00'),
                'end_time' => (new DateTime)->format('2018-11-21 14:00:00'),
            ], [
                'id' => 5,
                'rota_id' => 1,
                'staff_id' => 4,
                'start_time' => (new DateTime)->format('2018-11-21 12:00:00'),
                'end_time' => (new DateTime)->format('2018-11-21 18:00:00'),
            ], [
                'id' => 6,
                'rota_id' => 1,
                'staff_id' => 3,
                'start_time' => (new DateTime)->format('2018-11-22 08:00:00'),
                'end_time' => (new DateTime)->format('2018-11-22 18:00:00'),
            ], [
                'id' => 7,
                'rota_id' => 1,
                'staff_id' => 4,
                'start_time' => (new DateTime)->format('2018-11-22 08:00:00'),
                'end_time' => (new DateTime)->format('2018-11-22 18:00:00'),
            ]
        ]);

        DB::table('shift_breaks')->insert([
            [
                'id' => 1,
                'shift_id' => 6,
                'start_time' => (new DateTime)->format('2018-11-22 13:00:00'),
                'end_time' => (new DateTime)->format('2018-11-22 14:00:00'),
            ], [
                'id' => 2,
                'shift_id' => 7,
                'start_time' => (new DateTime)->format('2018-11-22 14:00:00'),
                'end_time' => (new DateTime)->format('2018-11-22 15:00:00'),
            ],
        ]);

    }
}
