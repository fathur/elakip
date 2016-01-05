<?php

namespace App\Http\Controllers\Privy\Period;

use App\Http\Controllers\Privy\AdminController;
use App\Models\Activity;
use App\Models\Agreement;
use App\Models\Goal;
use App\Models\Indicator;
use App\Models\Plan;
use App\Models\Program;
use App\Models\Target;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PhysicAchievementController extends AdminController
{
    /**
     * @return mixed
     * @author Fathur Rohman <fathur@dragoncapital.center>
     */
    public function getFilter()
    {
        $plans = [];
        foreach (Plan::with('period')->get() as $plan) {
            $plans[$plan->id] = $plan->period->year_begin . ' - ' . $plan->period->year_end;
        }

        $units = [];
        foreach (Unit::all() as $unit) {
            $units[$unit->id]   = $unit->name;
        }

        return view('private.physic_achievement.filter')
            ->with('plans', $plans)
            ->with('units', $units);
    }


    /**
     * @param Request $request
     * @return mixed
     * @author Fathur Rohman <fathur@dragoncapital.center>
     */
    public function getIndicator(Request $request)
    {
        $planId         = $request->get('plan'); // renstra
        $year           = $request->get('year');
        $targetId       = $request->get('target');
        $agreementId    = $request->get('agreement');
        $programId      = $request->get('program');
        $activityId     = $request->get('activity');
        $unitId     = $request->get('unit');

        $selectedAgreement = Agreement::with([
                'firstPosition' => function ($query) {
                    $query->with(['user','unit']);
                },
                'secondPosition' => function ($query) {
                    $query->with(['user','unit']);
                }
            ])
            ->where('year', $year)
            ->where('plan_id', $planId)
            ->get();

        $plan               = Plan::with(['period'])->find($planId);

        $selectedActivity   = Activity::where('program_id', $programId)
                                ->where('unit_id', $unitId)
                                ->get();

        $selectedTarget     = Target::activity($activityId)->get();

        $plans = [];
        foreach (Plan::with('period')->get() as $plan) {
            $plans[$plan->id] = $plan->period->year_begin . ' - ' . $plan->period->year_end;
        }

        $year_arr = $this->years;
        $agreement_arr = [];
        $program_arr = [];
        $activity_arr = [];
        $target_arr = [];

        foreach ($selectedAgreement as $item) {

            $agreement_arr[$item->id] = $item->firstPosition->user->name .
                ' (' . $item->firstPosition->unit->name . ') - ' .
                $item->secondPosition->user->name .
                ' (' . $item->secondPosition->unit->name . ')';
        }

        foreach ($plan->programs as $program) {
            $program_arr[$program->id] = $program->name;
        }

        foreach ($selectedActivity as $item)
            $activity_arr[$item->id] = $item->name;

        foreach ($selectedTarget as $item)
            $target_arr[$item->id] = $item->name;

        $unit_arr = [];
        foreach (Unit::all() as $unit) {
            $unit_arr[$unit->id] = $unit->name;
        }

        $plan = Plan::with(['period'])
            ->find($planId);

        $target = Target::with([
            'indicators' => function ($query) {
                $query->with(['goals' => function ($query) {
                    $query->with([
                        'achievements'
                    ]);
                    $query->whereBetween('year', [2015,2019]);
                    $query->orderBy('year', 'asc');
                }]);
            }
        ])
            ->find($targetId);

        //dd($target->indicators);

        $indicators = $this->reformatIndicators($target->indicators);

        //dd($indicators);

       return view('private.physic_achievement.detail')
           ->with('id', [
               'plan'      => $planId,
               'year'      => $year,
               'unit'      => $unitId,
               'agreement' => $agreementId,
               'program'   => $programId,
               'activity'  => $activityId,
               'target'  => $targetId,
           ])
           ->with('plans', $plans) //ok
           // ->with('period', $plan->period)
           ->with('indicators', $indicators)
           ->with('agreements', $agreement_arr) //ok
           ->with('programs', $program_arr) //ok
           ->with('activities', $activity_arr) //ok
           ->with('targets', $target_arr) //ok
           ->with('units', $unit_arr)
           ->with('years', $this->years); //ok
    }

    /**
     * @param Request $request
     * @return \BladeView|bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Fathur Rohman <fathur@dragoncapital.center>
     */
    public function getChart(Request $request, $indicatorId)
    {

        $goals = Goal::with(['achievements' => function($query) {
            $query->orderBy('quarter', 'asc');
        }])
            ->whereBetween('year', [2015,2019])
            ->where('indicator_id', $indicatorId)->get();

        //dd($goals->toArray());

        $year_holder = [];
        $count_holder = [];
        $real_holder = [];
        foreach ($goals as $goal) {
            array_push($year_holder, (int) $goal->year);
            array_push($count_holder, (int) $goal->count);
            array_push($real_holder, (int) $goal->achievements[3]->realization);
        }


        $years = $year_holder;
        $count = $count_holder;
        $real = $real_holder;

        return view('private.physic_achievement.chart')
            ->with('years', json_encode($years))
            ->with('count', json_encode($count))
            ->with('real', json_encode($real));
    }

    /**
     * @param Collection $indicators
     * @author Fathur Rohman <fathur@dragoncapital.center>
     * @return array
     */
    protected function reformatIndicators(Collection $indicators)
    {


        $indicatorsBucket = [
            'header'    => [],
            'data'      => []
        ];

        $header_years = [];

        foreach ($indicators as $indicator) {
            $data = [];

            foreach ($indicator->toArray() as $key => $val) {

                if($key != 'goals')
                    $data[$key] = $val;

                else {
                    $yearGoalHolder = [];
                    $yearAchHolder = [];
                    $totalGoalHolder = 0;
                    $totalAchHolder = 0;
                    foreach ($val as $goal) {
                        $yearGoalHolder[$goal['year']] = $goal['count'];
                        $yearAchHolder[$goal['year']] = $goal['achievements'][3]['realization']; // Ambil quarter 4 saja

                        $totalGoalHolder = $totalGoalHolder + $goal['count'];
                        $totalAchHolder = $totalAchHolder + $goal['achievements'][3]['realization']; // Ambil quarter 4 saja
                        array_push($header_years, $goal['year']);
                    }
                    $data['goal']['years'] = $yearGoalHolder;
                    $data['achievement']['years'] = $yearAchHolder;
                    $data['goal']['total']  = $totalGoalHolder;
                    $data['achievement']['total']  = $totalAchHolder;


                }
            }

            array_push($indicatorsBucket['data'], $data);

        }
        $indicatorsBucket['header']['years'] = array_unique($header_years);

        return $indicatorsBucket;

    }

    public function getChartOneYear($targetId, $year)
    {
        $target = Target::with(['indicators' => function ($query)  use ($year) {
            $query->with(['goals' => function ($query) use ($year) {

                $query->with(['achievements' => function ($query) {
                    $query->where('quarter', 4);
                }]);

                $query->where('year', $year);
            }]);
        }])
            ->find($targetId);
       /* if($target->type == 'activity')
        {
            $parent = Activity::find($target->id);
        }
        elseif($target->type == 'program')
        {
            $parent = Program::find($target->id);
        }*/

        //dd($target->toArray());

        $indicators = [];
        $count      = [];
        $real       = [];

        foreach ($target->indicators as $indicator) {
            array_push($indicators, $indicator->name);
            if(count($indicator->goals) > 0) {
                array_push($count, $indicator->goals[0]->count);

                if(count($indicator->goals[0]->achievements) > 0)
                {
                    array_push($real, $indicator->goals[0]->achievements[0]->realization);
                }
                else {
                    array_push($real, 0);

                }
            } else {
                array_push($count, 0);
            }
        }

        return view('private.physic_achievement.chart_one_year')
            ->with('indicators', json_encode($indicators))
            ->with('count', json_encode($count))
            ->with('real', json_encode($real));
    }
}
