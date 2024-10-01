<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // ランダムな10年間の期間を指定する処理
        $startYear = rand(1980, 2020); // ランダムな開始年を選択
        $endYear = $startYear + 9; // 開始年から10年間の範囲を指定
        $created_at = $this->faker->dateTimeBetween("{$startYear}-01-01", "{$endYear}-12-31");

        return [
            'customer_id' => rand(1, Customer::count()),
            'status' => $this->faker->boolean,
            'created_at' => $created_at
        ];
    }
}
