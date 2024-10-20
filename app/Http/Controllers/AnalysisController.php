<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Support\Facades\DB;


class AnalysisController extends Controller
{
    public function index()
    {
        $startDate = '2022-08-01';
        $endDate = '2022-08-31';

        // $period = Order::betweenDate($startDate, $endDate)
        // ->groupBy('id')
        // ->selectRaw('id, sum(subtotal) as total,
        // customer_name, status, created_at')
        // ->orderBy('created_at')
        // ->paginate(50);

        // dd($period);

        // $subQuery = Order::betweenDate($startDate, $endDate)
        // ->where('status', true)
        // ->groupBy('id')
        // ->selectRaw('id, sum(subtotal) as totalPerPurchase,
        // DATE_FORMAT(created_at, "%Y%m%d") as date');

        // $data = DB::table($subQuery)
        // ->groupBy('date')
        // ->selectRaw('date, sum(totalPerPurchase) as total')
        // ->get();

        // dd($data);

        // 購買IDごと
        $subQuery = Order::betweenDate($startDate, $endDate)
        ->groupBy('id')
        ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase');

        // 会員ごとで購入金額順にソート
        $subQuery = DB::table($subQuery)
        ->groupBy('customer_id')
        ->selectRaw('customer_id, customer_name, sum(totalPerPurchase) as total')
        ->orderBy('total', 'desc');

        // dd($subQuery);

        // 購入順に連番をつける
        DB::statement('set @row_num = 0;');
        $subQuery = DB::table($subQuery)
        ->selectRaw('
        @row_num:= @row_num+1 as row_num,
        customer_id,
        customer_name,
        total');

        // dd($subQuery);

        // 1/10の値、合計金額
        $count = DB::table($subQuery)->count();
        $total = DB::table($subQuery)->selectRaw('sum(total) as total')->get();
        $total = $total[0]->total;

        $decile = ceil($count / 10);

        $bindValues = [];
        $tempValue = 0;
        for($i =1; $i <=10; $i++)
        {
            array_push($bindValues, 1 + $tempValue);
            $tempValue += $decile;
            array_push($bindValues, 1+$tempValue);
        }

        // dd($count, $decile, $bindValues);

        // 10分割
        DB::statement('set @row_num = 0;');
        $subQuery = DB::table($subQuery)
        ->selectRaw("
            row_num,
            customer_id,
            customer_name,
            total,
            case
                when ? <= row_num and row_num < ? then 1
                when ? <= row_num and row_num < ? then 2
                when ? <= row_num and row_num < ? then 3
                when ? <= row_num and row_num < ? then 4
                when ? <= row_num and row_num < ? then 5
                when ? <= row_num and row_num < ? then 6
                when ? <= row_num and row_num < ? then 7
                when ? <= row_num and row_num < ? then 8
                when ? <= row_num and row_num < ? then 9
                when ? <= row_num and row_num < ? then 10
            end as decile
            ", $bindValues);

        // dd($subQuery);

        // デシルグループ毎に合計と平均
        $subQuery = DB::table($subQuery)
        ->groupBy('decile')
        ->selectRaw('decile, round(avg(total)) as average, sum(total) as totalPerGroup');

        // dd($subQuery);

        // 構成の比率
        DB::statement("set @total = ${total} ;");
        $data = DB::table($subQuery)
        ->selectRaw('decile, average, totalPerGroup, round(100 * totalPErGroup / @total, 1) as totalRadio') 
        ->get();    
        
        dd($data);
    
        return Inertia::render('Analysis');
    }
}
