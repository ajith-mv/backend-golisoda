<?php

namespace Database\Seeders;

use App\Models\GlobalSettings;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    
    public function run()
    {
        $ins['site_name'] = 'Goli Soda';
        $ins['site_email'] = 'info@golisodastore.com';
        $ins['site_mobile_no'] = '+91 73388 52311';
        $ins['address'] = 'No.4, 2nd Street, Dr.Radhakrishnan Salai, Mylapore, Chennai - 600004 Tamilnadu, India';

        GlobalSettings::updateOrCreate(['id' => 1], $ins);
       
    }
}
