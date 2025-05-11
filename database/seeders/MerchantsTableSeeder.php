<?php

namespace Database\Seeders;

use App\Models\merchants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        merchants::create([
            'user_id' => 3, // Gantilah dengan user_id yang valid
            'company_name' => 'Barber XYZ',
            'company_address' => 'Jl. aja No. 2B',
            'nik' => '123565799',
            'status' => 'approved',
            'city' => 'Tangerang',
            'description' => 'A barbershop providing excellent haircut services.',
        ]);
    }
}
