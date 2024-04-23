<?php

namespace Database\Seeders;

use App\Models\Product\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $ins['name']                = "LAPTOP";
        $ins['parent_id']           =   0;
        $ins['slug']                = Str::slug("LAPTOP");
        $ins['is_featured']         = '0';
        $ins['tax_id']              = 1;
        $ins['is_home_menu']        = "no";
        $ins['order_by']            = '1';
        $ins['added_by']            = '1';
       
        ProductCategory::updateOrCreate(['name' => 'LAPTOP'], $ins);

    }
}
