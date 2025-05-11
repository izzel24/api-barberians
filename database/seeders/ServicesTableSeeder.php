<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::create([
            'merchant_id' => 2, // Gantilah dengan merchant_id yang valid
            'name' => 'Haircut Regular',
            'description' => 'Regular haircut for men.',
            'price' => 50000,
        ]);
        
        Service::create([
            'merchant_id' => 2,
            'name' => 'Haircut and Creambath',
            'description' => 'hair care',
            'price' => 100000,
        ]);
    }
}
