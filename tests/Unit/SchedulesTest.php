<?php

namespace tests\Unit;

use App\Models\Shift;
use App\Models\Staff;
use Tests\TestCase;
use Carbon\Carbon;

class SchedulesTest extends TestCase
{

    public function testGetStaffInfoMethodError()
    {
        $response = $this->call('POST', 'SchedulesController@getStaffInfo', []);
        $this->assertEquals($response->getStatusCode(), 404);
    }

    public function testGetStaffInfoMethodSuccess()
    {
        $response = $this->call('POST', route('get.info'), ['rotaId' => 1, 'shopId' => 1]);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testGetStaffInfoMethodCatch()
    {
        $response = $this->call('POST', route('get.info'), ['rotaId' => 10, 'shopId' => 12]);
        $this->assertEquals($response->getStatusCode(), 422);
    }

    public function testGetStaffInfoMethodResponseDate()
    {
        $response = $this->call('POST', route('get.info'), ['rotaId' => 1, 'shopId' => 1]);
        $responseData = $response->decodeResponseJson('data');

        $shifts = Shift::select('start_time')->get()->groupBy(function ($shift) {
            return Carbon::parse($shift->start_time)->format('Y-m-d');
        });

        $dates = [];
        foreach ($shifts->toArray() as $date => $val) {
            $dates[] = $date;
        }
        foreach (array_keys($responseData) as $date) {
            $this->assertTrue(in_array($date, $dates));
        }

    }

    public function testGetStaffInfoMethodResponseStaff()
    {
        $response = $this->call('POST', route('get.info'), ['rotaId' => 1, 'shopId' => 1]);
        $responseData = $response->decodeResponseJson('data');

        $staff = Staff::select('first_name', 'surname')->get();
        $staffNames = [];
        foreach ($staff as $val) {
            $staffNames[] = $val->first_name . ' ' . $val->surname;
        }

        foreach ($responseData as $values) {
            foreach ($values as $staffName => $value) {
                $this->assertTrue(in_array($staffName, $staffNames));
            }
        }
    }


}
