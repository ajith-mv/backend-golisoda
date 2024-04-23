<?php

namespace Database\Seeders;

use App\Models\Master\Country;
use Illuminate\Database\Seeder;

class CountrySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insArray = array('iso' => 'IN', 'name' => 'INDIA', 'nice_name' => 'India', 'iso3'=>'AFG', 'num_code'=>356, 'phone_code' => 91);
        
        Country::updateOrCreate(['name' => 'INDIA'], $insArray);
    }
}
