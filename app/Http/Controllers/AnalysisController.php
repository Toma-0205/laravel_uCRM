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
        $startDate = '2021-09-01';
        $endDate = '2022-08-31';
    
        

        // RFM分析
        // 1.IDごと
        $subQuery = Order::betweenDate($startDate, $endDate)
        ->groupBy('id')
        ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase, created_at');

        // 2.顧客ごとに最終購入日、購入回数、購入合計金額を取得
        $subQuery = DB::table($subQuery)
        ->groupBy('customer_id')
        ->selectRaw('customer_id, customer_name, 
        max(created_at) as recentDate, 
        datediff(now(), max(created_at)) as recency, 
        count(customer_id) as frequency, 
        sum(totalPerPurchase) as monetary');

        // 会員ごとのRFMランクを計算
        $subQuery = DB::table($subQuery)
        ->selectRaw('customer_id, customer_name,
        recentDate, recency, frequency, monetary,
        case
        when recency <14 then 5
        when recency <28 then 4
        when recency <60 then 3
        when recency <90 then 2
        else 1 end as r,
        case
        when 7 <= frequency then 5
        when 5 <= frequency then 4
        when 3 <= frequency then 3
        when 2 <= frequency then 2
        else 1 end as f,
        case
        when 300000 <= monetary then 5
        when 200000 <= monetary then 4
        when 100000 <= monetary then 3
        when 30000 <= monetary then 2
        else 1 end as m');

        // ランクごとの数を計算
        $rCount = DB::table($subQuery)
        ->groupBY('r')
        ->selectRaw('r, count(r)')
        ->orderBy('r', 'desc')
        ->get();

        $fCount = DB::table($subQuery)
        ->groupBY('f')
        ->selectRaw('f, count(f)')
        ->orderBy('f', 'desc')
        ->get();

        $mCount = DB::table($subQuery)
        ->groupBy('m')
        ->selectRaw('m, count(m)')
        ->orderBy('m', 'desc')
        ->get();

        $total = DB::table($subQuery)->count();

        dd($rCount, $fCount, $mCount, $total);



        return Inertia::render('Analysis');
    }
}
