<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\Propoal;
use App\Exports\ViewExport;
use Carbon\Carbon;
use DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
class ReportController extends Controller
{
    protected $first_date_month;
    protected $last_date_month;

    public function __construct()
    {
        $this->first_date_month = new Carbon('first day of this month');
        $this->last_date_month = new Carbon('last day of this month');

    }


    public function effectiveness(Request $request)
    {

        $since = (!empty($request->since)) ? Carbon::parse($request->since) : $this->first_date_month;
        $until = (!empty($request->until)) ? Carbon::parse($request->until) : $this->last_date_month;
        $users = User::select('*');
        if(!empty($request->office_id))
        {
            $users->whereHas('offices',function($q) use($request) {
                if(!empty($request->office_id))
                {
                    $q->where('office_id', $request->office_id);
                }

            });
        }

        $users = $users->get();



        $data = [];
        foreach( $users as $user )
        {
            $propoals = $user->propoals()->where('owner_user_id',$user->id);
            if(!empty($request->since))
            {
                $propoals->whereDate('created_at','>=',$since->format('Y-m-d'));
            }

            if(!empty($request->until))
            {
                $propoals->whereDate('created_at','<=',$until->format('Y-m-d'));
            }
            if(!empty($request->office_id))
            {
                $propoals->where('office_id',$request->office_id);
            }
            $propoals = $propoals->get();

            $user->propoals = $propoals->count();


            $propoalsApproved = $user->propoals()->where('owner_user_id',$user->id)
            ->whereHas('status',function($q) use($since, $until){
                $q->where('name','Aprobada');
            });

            if(!empty($request->since))
            {
                $propoalsApproved->whereDate('created_at','>=',$since->format('Y-m-d'));
            }

            if(!empty($request->until))
            {
                $propoalsApproved->whereDate('created_at','<=',$until->format('Y-m-d'));
            }
            if(!empty($request->office_id))
            {
                $propoalsApproved->where('office_id',$request->office_id);
            }

            $propoalsApproved = $propoalsApproved->get();

            $user->total_imorte = $propoalsApproved->sum('total');
            $user->propoals_approved = $propoalsApproved->count();
            $user->effectiveness = ($propoals->count()>0) ? ($propoalsApproved->count()*100)/ $propoals->count() : 0;
            $user->import = $propoalsApproved->sum('total');
        }

        $users = $users->sortByDesc('propoals_approved');
        $params =  [
            'data'      =>  $users,
            'since'     =>  $since,
            'until'     =>  $until,
            'view'      =>  'reports.excel.effectiveness'
        ];
        switch( $request->format )
        {
            case 'pdf':
                $pdf = PDF::loadView('reports.pdf.effectiveness', $params)
                ->setOption('margin-top', 16)
                ->setOption('margin-bottom', 16)
                ->setOption('margin-right', 16)
                ->setOption('margin-left', 16);
                return $pdf->inline('effectiveness.pdf');
                break;

            case 'excel':
                return Excel::download(
                    new ViewExport (
                        $params
                    ),
                    'effectiveness.xlsx'
                );
                break;

            default:
                return response()->json($users,200);
            break;
        }


    }
    public function sales(Request $request)
    {
        $since = (!empty($request->since)) ? Carbon::parse($request->since) : $this->first_date_month;
        $until = (!empty($request->until)) ? Carbon::parse($request->until) : $this->last_date_month;

        $clients = Client::where('is_prospect',false)
        ->get();

        foreach($clients as $client)
        {
            $quotations = Quotation::whereHas('status',function($q){
                $q->whereIn('name',['Aprobada','Aprobada y pagada']);
            })
            ->where('client_id',$client->id);
            if(!empty($request->office_id))
            {
                $quotations->whereHas('propoal',function($q) use($request){
                    $q->where('office_id',$request->office_id);
                });
            }

            if( !empty($request->since) )
            {
                $quotations->whereDate('approved_date','>=',$since->format('Y-m-d'));
            }

            if( !empty($request->until) )
            {
                $quotations->whereDate('approved_date','<=',$until->format('Y-m-d'));
            }


            $quotations = $quotations->get();

            $client->sales = $quotations->count();
            $client->import = $quotations->sum('total_pesos');
        }

        $clients = $clients->sortByDesc('sales');

        $params =  [
            'data'      =>  $clients,
            'since'     =>  $since,
            'until'     =>  $until,
            'view'      =>  'reports.excel.sales'
        ];

        switch( $request->format )
        {
            case 'pdf':
                $pdf = PDF::loadView('reports.pdf.sales', $params)
                ->setOption('margin-top', 16)
                ->setOption('margin-bottom', 16)
                ->setOption('margin-right', 16)
                ->setOption('margin-left', 16);
                return $pdf->inline('sales.pdf');
                break;

            case 'excel':
                return Excel::download(
                    new ViewExport (
                        $params
                    ),
                    'sales.xlsx'
                );
                break;

            default:
                return response()->json($sales,200);
            break;
        }
    }

    public function client_quotations(Request $request)
    {
        $request->validate([
            'client_id'     =>  'required|array|min:1',
            'since'         =>  'nullable|date_format:Y-m-d',
            'until'         =>  'nullable|date_format:Y-m-d'
        ]);
        $since = (!empty($request->since)) ? Carbon::parse($request->since) : $this->first_date_month;
        $until = (!empty($request->until)) ? Carbon::parse($request->until) : $this->last_date_month;

        $clients = Client::whereIn('id',$request->client_id)->get();

        $params =  [
            'clients'   =>  $clients,
            'since'     =>  $since,
            'until'     =>  $until,
            'view'      =>  'reports.excel.client_quotations'
        ];

        switch( $request->format )
        {
            case 'pdf':
                $pdf = PDF::loadView('reports.pdf.client_quotations', $params)
                ->setOption('margin-top', 16)
                ->setOption('margin-bottom', 16)
                ->setOption('margin-right', 16)
                ->setOption('margin-left', 16);
                return $pdf->inline('client_quotations.pdf');
                break;

            case 'excel':
                return Excel::download(
                    new ViewExport (
                        $params
                    ),
                    'client_quotations.xlsx'
                );
                break;

            default:
                return response()->json($client,200);
            break;
        }
    }

}
