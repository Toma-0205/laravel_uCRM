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
        sum(totalPerPurchase) as monetary')->get();

        dd($subQuery);

        return Inertia::render('Analysis');
    }
}
