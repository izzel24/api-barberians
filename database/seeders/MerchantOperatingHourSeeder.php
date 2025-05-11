<?php

namespace Database\Seeders;

use App\Models\MerchantOperatingHour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantOperatingHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MerchantOperatingHour::create([
            'merchant_id' => 2,  // Ganti dengan ID merchant yang sesuai
            'day_of_week' => 'Monday',
            'open_time' => '09:00:00',
            'close_time' => '16:00:00',
        ]);

        MerchantOperatingHour::create([
            'merchant_id' => 2,  // Ganti dengan ID merchant yang sesuai
            'day_of_week' => 'Tuesday',
            'open_time' => '09:00:00',
            'close_time' => '18:00:00',
        ]);
    }
}
