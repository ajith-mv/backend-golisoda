<?php

namespace Database\Seeders;

use App\Models\Category\MainCategory;
use App\Models\Category\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AddressTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ins['category_name']       = "Address Type";        
        $ins['slug']                = Str::slug("Address Type");
        $ins['status']              = 'published';
        $ins['added_by']            = '1';
       
        $main_category = MainCategory::updateOrCreate(['category_name' => 'Address Type'], $ins);

        $ins1['parent_id']  = $main_category->id;
        $ins1['name']       = 'Home';
        $ins1['slug']       = Str::slug("Home");
        $ins1['order_by']   = 1;
        $ins1['status']     = 'published';
        $ins1['added_by']   = '1';

        SubCategory::updateOrCreate(['parent_id' => $main_category->id, 'name' => 'Home'], $ins1);

        $ins2['parent_id']  = $main_category->id;
        $ins2['name']       = 'Work';
        $ins2['slug']       = Str::slug("Work");
        $ins2['order_by']   = 1;
        $ins2['status']     = 'published';
        $ins2['added_by']   = '1';

        SubCategory::updateOrCreate(['parent_id' => $main_category->id, 'name' => 'Work'], $ins2);

        $ins3['category_name']       = "Product Labels";        
        $ins3['slug']                = Str::slug("Product Labels");
        $ins3['status']              = 'published';
        $ins3['added_by']            = '1';
       
        $main_category = MainCategory::updateOrCreate(['category_name' => 'Product Labels'], $ins3);

    }
}
