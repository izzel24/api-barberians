<?php

namespace Database\Seeders;

use App\Models\MerchantPhoto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantPhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MerchantPhoto::create([
            'merchant_id' => 2, // Gantilah dengan merchant_id yang valid
            'path' => 'images/merchant/federico-tonini-tdDPj4Jpwu4-unsplash.jpg',
        ]);
        
        MerchantPhoto::create([
            'merchant_id' => 2,
            'path' => 'images/merchant/andre-reis-1_DAlXy0wng-unsplash.jpg',
        ]);
    }
}
