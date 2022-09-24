<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TaskRepositoryEloquent;
use App\Repositories\PropoalRepositoryEloquent;
use App\Repositories\QuotationRepositoryEloquent;
use App\Repositories\ClientRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\UserGoalRepositoryEloquent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Auth;

class DashboardController extends Controller
{

    protected $taskRepository;
    protected $clientRepository;
    protected $propoalRepository;
    protected $quotationRepository;
    protected $userRepository;
    protected $userGoalRepository;
    protected $user;

    public function __construct(
        TaskRepositoryEloquent $taskRepository,
        PropoalRepositoryEloquent $propoalRepository,
        QuotationRepositoryEloquent $quotationRepository,
        ClientRepositoryEloquent $clientRepository,
        UserGoalRepositoryEloquent $userGoalRepository,
        UserRepositoryEloquent $userRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->clientRepository = $clientRepository;
        $this->propoalRepository = $propoalRepository;
        $this->quotationRepository = $quotationRepository;
        $this->userRepository = $userRepository;
        $this->user = Auth::user();
    }

    public function tasksOpens(Request $request)
    {

        $request->validate([
            'office_id'     =>  'required'
        ]);
        $this->taskRepository->pushCriteria(\App\Criteria\TaskTypeCriteria::class);
        return $this->taskRepository
        ->with(['client','propoal.status','status','task_type','files','owner'])
        ->whereHas('status',function($q){
            $q->whereIn('name',['Pendiente','Vencida']);
        })->get();
    }

    public function approvedSalesMonth(Request $request)
    {
        $request->validate([
            'office_id'     =>  'required'
        ]);

        $start = new Carbon('first day of this month');
        $request->initialize(['since'=>$start->format('Y-m-d')]);
        if(Auth::user()->hasRole('Vendedor'))
        {
            $request->initialize(['owner_user_id'=>Auth::user()->id]);
        }
        $this->quotationRepository->pushCriteria(\App\Criteria\QuotationCriteria::class);
        return $resp = $this->quotationRepository->with([
            'client',
            'propoal.owner'
        ])
        ->whereHas('status',function($q){
            $q->whereIn('name',['Aprobada','Aprobada y pagada']);
        })->get();

    }
    public function possibleSales(Request $request)
    {
        $request->validate([
            'office_id'     =>  'required'
        ]);
        if(Auth::user()->hasRole('Vendedor'))
        {
            $request->initialize(['owner_user_id'=>Auth::user()->id]);
        }
        $this->quotationRepository->pushCriteria(\App\Criteria\QuotationCriteria::class);
        return $resp = $this->quotationRepository->with([
            'client',
            'propoal.owner'
        ])
        ->whereHas('status',function($q){
            $q->whereIn('name',['Abierta','Vencida']);
        })->get();

    }

    public function graphSalesYear(Request $request)
    {
        $request->validate([
            'office_id'     =>  'required'
        ]);
        $now = Carbon::now();
        $period = CarbonPeriod::create($now->startOfYear()->format('Y-m-d'), '1 month', $now->endOfYear()->format('Y-m-d'));
        $resp = [];
        foreach ($period as $dt) {
            DB::enableQueryLog();

            $q = $this->quotationRepository
            ->whereHas('status',function($q){
                $q->whereIn('name',['Aprobada','Aprobada y pagada']);
            })
            ->whereHas('propoal',function($q) use($request){
                if(Auth::user()->hasRole('Vendedor'))
                {
                    $q->where('owner_user_id',Auth::user()->id);
                }
                $q->where('office_id',$request->office_id);
            })
            ->findWhereBetween(DB::raw('DATE(created_at)'),[
                $dt->firstOfMonth()->format('Y-m-d'),
                $dt->lastOfMonth()->format('Y-m-d')
            ],[
                DB::raw("(exchange_rate * total) as total_pesos"),
                "exchange_rate"
            ]);

            $resp[__('months.'.$dt->format('F'))] = $q->sum('total_pesos');
        }

        return $resp;

    }

    public function goals_graphs(Request $request)
    {
        $request->validate([
            'office_id'     =>  'required',
            'users'         =>  'nullable|array'
        ]);
        $users = [];
        if(Auth::user()->hasRole('Vendedor'))
        {
            $users[] = Auth::user();

        }else{
            $users = $this->userRepository ->whereHas('offices',function($q) use($request){
                $q->where('offices.id',$request->office_id);
            })->with('offices')->findWhereIn('id',$request->users);
        }
        $resp = [];
        foreach( $users as $i => $user )
        {
            $goal = $user->goals()->orderBy('created_at','desc')->first();

            if($goal)
            {
                $amount = 0;
                $propoals = $this->propoalRepository

                ->whereHas('status',function($q){
                    $q->whereIn('name',['Aprobada y Pagada']);
                })
                ->findWhere([
                    ['owner_user_id','=',$user->id],
                    ['created_at','>=',$goal->start_date]
                ]);

                foreach($propoals as $propoal)
                {
                    $quotations = $propoal->quotations()
                    ->whereHas('status',function($q){
                        $q->whereIn('name',['Aprobada','Aprobada y Pagada']);
                    })
                    ->whereBetween('approved_date',[$goal->start_date, $goal->end_date])
                    ->with([
                        'details'           =>  function($q){
                            $q->where('confirm',true);
                            $q->whereHas('service',function($q){
                                $q->where('utility',true);
                                $q->orWhere('operator_commission',true);
                            });
                        },


                    ])
                    ->get();
                    foreach($quotations as $quotation)
                    {
                        $amount += ($quotation->details->sum('import')) * $quotation->exchange_rate;
                    }
                }
                $goal->progress = ($amount * 100)/$goal->amount;
                $goal->rechargeable_amount = $amount;

                $user->goal = $goal;
                $resp[] = $user;
            }
        }
        return $resp;
    }

    public function propoals_graphs(Request $request)
    {
        $request->validate([
            'office_id'     =>  'required'
        ]);

        if(Auth::user()->hasRole('Vendedor'))
        {
            $request->initialize(['owner_user_id'=>Auth::user()->id]);
        }
        $this->propoalRepository->pushCriteria(\App\Criteria\PropoalCriteria::class);

        $opens = $this->propoalRepository->whereHas('status',function($q){
            $q->whereIn('name',['Abierta','Vencida']);
        })->get()->count();

        $closeds = $this->propoalRepository->whereHas('status',function($q){
            $q->whereNotIn('name',['Abierta','Vencida']);
        })->get()->count();

        $total = $opens + $closeds;

        return [
            'Abiertas'      =>  ($total==0) ? 0 : round(($opens*100)/$total,2),
            'Cerradas'      =>  ($total==0) ? 0 : round(($closeds*100)/$total,2),
        ];
    }
}
