<?php

namespace App\Http\Controllers\Privy\Period;

use App\Http\Controllers\Privy\AdminController;
use App\Models\Activity;
use App\Models\Agreement;
use App\Models\Goal;
use App\Models\Plan;
use App\Models\Program;
use App\Models\Target;
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

        return view('private.physic_achievement.filter')
            ->with('plans', $plans)
            ->with('years', $this->years);
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

        $selectedProgram    = Program::where('plan_id', $planId)->get();
        $selectedActivity   = Activity::where('program_id', $programId)->get();
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

        foreach ($selectedProgram as $item)
            $program_arr[$item->id] = $item->name;

        foreach ($selectedActivity as $item)
            $activity_arr[$item->id] = $item->name;

        foreach ($selectedTarget as $item)
            $target_arr[$item->id] = $item->name;

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

           ->with('years', $this->years); //ok
    }

    /**
     * @return \BladeView|bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Fathur Rohman <fathur@dragoncapital.center>
     */
    public function getChart()
    {
        return view('private.physic_achievement.chart');
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
}
