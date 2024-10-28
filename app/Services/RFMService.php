<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;


class RFMService
{
    public static function rfm($subQuery, $rfmPrms)
    {
        // RFM分析
        // 1.IDごと
        $subQuery = $subQuery->groupBy('id')
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
        // $rfmPrms = [
        //     14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000
        // ];

        $subQuery = DB::table($subQuery)
        ->selectRaw('
            customer_id, customer_name,
            recentDate, recency, frequency, monetary,
            case
            when recency < ? then 5
            when recency < ? then 4
            when recency < ? then 3
            when recency < ? then 2
            else 1 end as r,
            case
            when ? <= frequency then 5
            when ? <= frequency then 4
            when ? <= frequency then 3
            when ? <= frequency then 2
            else 1 end as f,
            case
            when ? <= monetary then 5
            when ? <= monetary then 4
            when ? <= monetary then 3
            when ? <= monetary then 2
            else 1 end as m',
            $rfmPrms
        );

        // // ランクごとの数を計算
        // $total = DB::table($subQuery)->count();

        // $rCount = DB::table($subQuery)
        // ->groupBY('r')
        // // ->selectRaw('r, count(r)')
        // ->selectRaw('r, count(r) as r_count')
        // ->orderBy('r', 'desc')
        // // ->pluck('count(r)');
        // // ->pluck('r_count');
        // ->get();

        // $fCount = DB::table($subQuery)
        // ->groupBY('f')
        // // ->selectRaw('f, count(f)')
        // ->selectRaw('f, count(f) as f_count')
        // ->orderBy('f', 'desc')
        // // ->pluck('count(f)');
        // // ->pluck('f_count');
        // ->get();

        // $mCount = DB::table($subQuery)
        // ->groupBy('m')
        // // ->selectRaw('m, count(m)')
        // ->selectRaw('m, count(m) as m_count')
        // ->orderBy('m', 'desc')
        // // ->pluck('count(m)');
        // // ->pluck('m_count');
        // ->get();

        // $rCount = $rData->pluck('r_count');
        // $fCount = $fData->pluck('f_count');
        // $mCount = $mData->pluck('m_count');

        // ランクごとの数を計算
        $totals = DB::table($subQuery)->count();

        // rCount, fCount, mCount を取得する前に、まず $rData, $fData, $mData にデータを取得
        $rData = DB::table($subQuery)
            ->groupBY('r')
            ->selectRaw('r, count(r) as r_count')
            ->orderBy('r', 'desc')
            ->get();

        $fData = DB::table($subQuery)
            ->groupBY('f')
            ->selectRaw('f, count(f) as f_count')
            ->orderBy('f', 'desc')
            ->get();

        $mData = DB::table($subQuery)
            ->groupBy('m')
            ->selectRaw('m, count(m) as m_count')
            ->orderBy('m', 'desc')
            ->get();

        // データからカウントを抽出
        $rCount = $rData->pluck('r_count');
        $fCount = $fData->pluck('f_count');
        $mCount = $mData->pluck('m_count');


        $eachCount = [];
        $rank = 5;

        for($i = 0; $i < 5; $i++)
        {
            array_push($eachCount, [
                'rank' => $rank,
                'r' => isset($rCount[$i]) ? $rCount[$i] : 0,  // インデックスが存在しない場合は0を返す
                'f' => isset($fCount[$i]) ? $fCount[$i] : 0,
                'm' => isset($mCount[$i]) ? $mCount[$i] : 0,
            ]);
            $rank--;
        }

        // dd($rCount, $eachCount ,$fCount, $mCount, $total);
        // dd($rCount, $fCount, $mCount);


        // RとFで２次元表示
        $data = DB::table($subQuery)
        ->groupBy('r')
        ->selectRaw('
            CONCAT("r_", r) AS rRank,
            COUNT(CASE WHEN f = 5 THEN 1 END) AS f_5,
            COUNT(CASE WHEN f = 4 THEN 1 END) AS f_4,
            COUNT(CASE WHEN f = 3 THEN 1 END) AS f_3,
            COUNT(CASE WHEN f = 2 THEN 1 END) AS f_2,
            COUNT(CASE WHEN f = 1 THEN 1 END) AS f_1
        ')
        ->orderBy('rRank', 'desc')
        ->get();

        // dd($data);

        return [$data, $totals, $eachCount];
    }
}