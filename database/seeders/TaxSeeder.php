<?php

namespace Database\Seeders;

use App\Models\Settings\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ins['title']               = 'Tax 18';
        $ins['pecentage']           = 18;
        $ins['order_by']            = '1';
        Tax::updateOrCreate(['title' => 'Tax 18'], $ins);
       
    }
}
