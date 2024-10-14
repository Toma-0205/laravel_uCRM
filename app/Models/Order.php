<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Subtotal;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new Subtotal);
    }

    public function scopeBetweenDate($query, $startDate = null, $endDate = null)
    {
        // 開始日と終了日が両方とも指定されていない場合、元のクエリをそのまま返す
        if (is_null($startDate) && is_null($endDate)) 
        {
            return $query;
        }

        // 開始日のみが指定されている場合、開始日以降のデータを取得
        if (!is_null($startDate) && is_null($endDate)) 
        {
            return $query->where('created_at', '>=', $startDate);
        }

        // 終了日のみが指定されている場合、終了日以前のデータを取得
        if (is_null($startDate) && !is_null($endDate))
        {
            $endDate1 = Carbon::parse($endDate)->addDays(1);
            return $query->where('created_at', '<=', $endDate1);
        }

        // 開始日と終了日が両方指定されている場合、期間内のデータを取得
        if (!is_null($startDate) && !is_null($endDate)) 
        {
            $endDate1 = Carbon::parse($endDate)->addDays(1);
            return $query->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate1);
        }
    }

}
