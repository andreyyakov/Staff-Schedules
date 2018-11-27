<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Http\Request;
use App\Models\{
    Rota, Shop
};
use Carbon\Carbon;


class SchedulesController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function show()
    {

        $rotas = Rota::select('id', 'week_commence_date')->get();
        $shops = Shop::select('id', 'name')->get();

        return view('schedules', ['rotas' => $rotas, 'shops' => $shops]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStaffInfo(Request $request)
    {
        $validator = Validator::make($request->only('rotaId', 'shopId'), [
            'rotaId' => 'required',
            'shopId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator], 404);
        }

        try {
            $rotaId = $request->only('rotaId');
            $shopId = $request->only('shopId');

            $rota = Rota::with('shifts', 'shifts.staff')->where('id', $rotaId)->where('shop_id', $shopId)->first();
            $schedule = $rota->shifts->groupBy(function ($shift) {
                return Carbon::parse($shift->start_time)->format('Y-m-d');
            });

            $singleManningByDate = [];
            foreach ($schedule as $date => $shifts) {
                $workTimeIntervals = [];

                foreach ($shifts as $shift) {
                    $workTimeIntervals[$shift->staff->first_name . ' ' . $shift->staff->surname] = $this->calcWorkIntervals($shift);
                }
                $singleManning = [];
                if (count($workTimeIntervals) === 2) { // work not alone

                    foreach ($workTimeIntervals as $key => $interval) {

                        if (count($interval) === 1) { // has not break time
                            $nextInterval = next($workTimeIntervals);
                            if ($nextInterval) {
                                list($singleManning[$key], $singleManning[key($workTimeIntervals)]) =
                                    $this->calculateNoOverlap($interval[0]['start_time'], $interval[0]['end_time'], $nextInterval[0]['start_time'], $nextInterval[0]['end_time']);
                            } else {
                                continue;
                            }
                        } elseif (count($interval) === 2) { // has break time
                            $nextInterval = next($workTimeIntervals);

                            if ($nextInterval) {
                                $a1 = [
                                    'start_time' => $interval[0]['start_time'],
                                    'end_time' => $interval[0]['end_time'],
                                ];
                                $a2 = [
                                    'start_time' => $interval[1]['start_time'],
                                    'end_time' => $interval[1]['end_time'],
                                ];
                                $b1 = [
                                    'start_time' => $nextInterval[0]['start_time'],
                                    'end_time' => $nextInterval[0]['end_time'],
                                ];
                                $b2 = [
                                    'start_time' => $nextInterval[1]['start_time'],
                                    'end_time' => $nextInterval[1]['end_time'],
                                ];
                                list($singleManning[$key], $singleManning[key($workTimeIntervals)]) = $this->calculateForRanges($a1, $a2, $b1, $b2);
                            } else {
                                continue;
                            }
                        }
                    }
                } else {
                    foreach ($workTimeIntervals as $key => $interval) {
                        $singleManning[$key] = Carbon::parse($interval[0]['end_time'])->diffInMinutes(Carbon::parse($interval[0]['start_time']));
                    }
                }
                $singleManningByDate[$date] = $singleManning;

            }

            return response()->json(['status' => 'success', 'data' => $singleManningByDate], 200);

        } catch (\Exception $e) {
            Log::error('An error has occurred in file ' . $e->getFile() . ' In line' . $e->getLine() . ' Error --' . $e->getMessage());
            return response()->json(['status' => 'error'], 422);
        }
    }


    /**
     * @param collection $shift
     * @return array $intervals
     */
    private function calcWorkIntervals($shift)
    {
        $intervals = [];
        $breaks = $shift->breaks()->get();

        if ($breaks->isNotEmpty()) {
            foreach ($breaks as $key => $break) {

                if ($key === 0) {
                    $intervals[] = ['start_time' => $shift->start_time, 'end_time' => $break->start_time];
                    if (isset($breaks[$key + 1])) {
                        $intervals[] = ['start_time' => $break->end_time, 'end_time' => $break[$key + 1]->start_time];
                    } else {
                        $intervals[] = ['start_time' => $break->end_time, 'end_time' => $shift->end_time];
                    }
                } else {
                    if (!isset($breaks[$key + 1])) {
                        $intervals[] = ['start_time' => $break->end_time, 'end_time' => $shift->end_time];
                    } else {
                        $intervals[] = ['start_time' => $break->end_time, 'end_time' => $breaks[$key + 1]->start_time];
                    }
                }
            }
        } else {
            $intervals[] = ['start_time' => $shift->start_time, 'end_time' => $shift->end_time];
        }

        return $intervals;

    }

    /**
     * @param $periodStart string
     * @param $periodEnd string
     * @param $timeIn string
     * @param $timeOut string
     * @return array|int
     */
    private function calculateNoOverlap($periodStart, $periodEnd, $timeIn, $timeOut)
    {

        $periodStart = Carbon::parse($periodStart);
        $periodEnd = Carbon::parse($periodEnd);
        $timeIn = Carbon::parse($timeIn);
        $timeOut = Carbon::parse($timeOut);

        if ($periodStart->gte($timeIn) && $periodEnd->lte($timeOut)) {
            // The compared time range can be contained within borders of the source time range, so the over lap is the entire compared time range
            $overlap = $periodEnd->diffInMinutes($periodStart);
            return [0, ($timeOut->diffInMinutes($timeIn)) - $overlap];
        } elseif ($periodStart->gte($timeIn) && $periodStart->lte($timeOut)) {
            // The compared time range starts after or at the source time range but also ends after it because it failed the condition above
            return [$periodEnd->diffInMinutes($timeOut), $periodStart->diffInMinutes($timeIn)];
        } elseif ($periodEnd->gte($timeIn) && $periodEnd->lte($timeOut)) {
            // The compared time range starts before the source time range and ends before the source end time
            return [$timeIn->diffInMinutes($periodStart), $timeOut->diffInMinutes($periodEnd)];
        } elseif ($timeIn->gt($periodStart) && $timeOut->lt($periodEnd)) {
            // The compared time range is actually wider than the source time range, so the overlap is the entirety of the source range
            $overlap = $timeOut->diffInMinutes($timeIn);
            return [($periodEnd->diffInMinutes($periodStart)) - $overlap, 0];
        }
        return 0;
    }

    /**
     * @param $a1 array
     * @param $a2 array
     * @param $b1 array
     * @param $b2 array
     * @return array|int
     */
    private function calculateForRanges($a1, $a2, $b1, $b2)
    {
        $a1 = $this->dateParse($a1);
        $a2 = $this->dateParse($a2);

        $b1 = $this->dateParse($b1);
        $b2 = $this->dateParse($b2);


        if ($a1['start_time']->eq($b1['start_time']) && $a2['end_time']->eq($b2['end_time'])) {

            if ($a1['end_time']->gt($b1['end_time'])) {
                if ($a1['end_time']->gte($b2['start_time'])) {
                    return [0, $b2['start_time']->diffInMinutes($b1['end_time'])];
                } elseif ($a1['end_time']->lt($b2['start_time'])) {
                    if ($b2['start_time']->gte($a2['start_time'])) {
                        return [0, ($b2['start_time']->diffInMinutes($b1['end_time'])) - ($a2['start_time']->diffInMinutes($a1['end_time']))];
                    } elseif ($b2['start_time']->lt($a2['start_time'])) {
                        return [$a2['start_time']->diffInMinutes($b2['start_time']), $a1['end_time']->diffInMinutes($b1['end_time'])];
                    }
                }
            } elseif ($a1['end_time']->lt($b1['end_time'])) {
                if ($b1['end_time']->gte($a2['start_time'])) {
                    return [$a2['start_time']->diffInMinutes($a1['end_time']), $b2['start_time']->diffInMinutes($b1['end_time'])];
                } elseif ($b1['end_time']->lt($a2['start_time'])) {
                    if ($a2['start_time']->gte($b2['start_time'])) {
                        return [($a2['start_time']->diffInMinutes($a1['end_time'])) - ($b2['start_time']->diffInMinutes($b1['end_time'])), 0];
                    } elseif ($a2['start_time']->lt($b2['start_time'])) {
                        return [$b1['end_time']->diffInMinutes($a1['end_time']), $b2['start_time']->diffInMinutes($a2['start_time'])];
                    }
                }
            } elseif ($a1['end_time']->eq($b1['end_time'])) {
                if ($a2['start_time']->gte($b2['start_time'])) {
                    return [$a2['start_time']->diffInMinutes($b2['start_time']), 0];
                } elseif ($a2['start_time']->lt($b2['start_time'])) {
                    return [0, $b2['start_time']->diffInMinutes($a2['start_time'])];
                }
            }
        }
        return 0;
    }

    /**
     * @param array $date
     * @return array
     */
    private function dateParse($date)
    {
        $date['start_time'] = Carbon::parse($date['start_time']);
        $date['end_time'] = Carbon::parse($date['end_time']);
        return $date;
    }


}
