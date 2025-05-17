<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\LaravelIgnition\Support\Composer\FakeComposer;
use Faker\Factory as Faker;

class QueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Ambil beberapa user_id dan merchant_id yang sudah ada di database
        $userIds = [2, 6, 7, 9, 10]; // ambil id user
        $merchantIds = \App\Models\merchants::pluck('id')->toArray(); // ambil id merchant

        // Generate 10 antrian dummy untuk masing-masing merchant
        foreach ($merchantIds as $merchantId) {
    foreach (range(1, 10) as $index) {
        $queueData = [
            'user_id' => $faker->randomElement($userIds),
            'merchant_id' => $merchantId,
            'service_id' => $faker->numberBetween(1, 2),
            'date' => $faker->date(),
            'time' => $faker->time(),
            'status' => $faker->randomElement(['pending', 'accepted', 'rejected']),
            'queue_number' => $index,
            'total_price' => $faker->numberBetween(100000, 500000),
           'created_at' => Carbon::now()->toDateTimeString(),  
            'updated_at' => Carbon::now()->toDateTimeString(), 
        ];

        dd($queueData); // Menampilkan data yang akan dimasukkan ke dalam tabel

        DB::table('queues')->insert($queueData);
    }
}
    }
    
}
