<?php

namespace Database\Seeders;

use App\Models\Master\Brands;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Image;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $ins1['brand_name']          = "ACER";
        $ins1['order_by']            = '1';
        $ins1['added_by']            = '1';
        $ins1['slug']                = Str::slug("ACER");

        $check = Brands::where('brand_name', 'ACER')->first();

        if( !$check ) {

            $data1 = Brands::create($ins1);
            $brand_id = $data1->id;
            $directory = 'brands/'.$brand_id;
            Storage::deleteDirectory('public/'.$directory);
            $imageName              = 'acer.jpg';
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/option1"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/option1"), 0775, true);
            }
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/default"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/default"), 0775, true);
            }
            $data1->brand_logo       = $imageName;
            $data1->update();
        }


        
        $ins2['brand_name']          = "ASUS";
        $ins2['order_by']            = '2';
        $ins2['added_by']            = '1';
        $ins2['slug']                = Str::slug("ASUS");

        $check1 = Brands::where('brand_name', 'ASUS')->first();
        if( !$check1 ) {

            $data2 = Brands::create($ins2);
    
            $brand_id = $data2->id;
            $directory = 'brands/'.$brand_id;
            Storage::deleteDirectory('public/'.$directory);
            $imageName              = 'asus.jpg';
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/option1"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/option1"), 0775, true);
            }
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/default"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/default"), 0775, true);
            }
            $data2->brand_logo       = $imageName;
            $data2->update();

        }


        $ins3['brand_name']          = "DELL";
        $ins3['order_by']            = '3';
        $ins3['added_by']            = '1';
        $ins3['slug']                = Str::slug("DELL");

        $check2 = Brands::where('brand_name', 'DELL')->first();

        if( !$check2 ) {

            $data3 = Brands::create($ins3);
    
            $brand_id = $data3->id;
            $directory = 'brands/'.$brand_id;
            Storage::deleteDirectory('public/'.$directory);
            $imageName              = 'dell.jpg';
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/option1"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/option1"), 0775, true);
            }
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/default"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/default"), 0775, true);
            }
            $data3->brand_logo       = $imageName;
            $data3->update();
        }

        $ins4['brand_name']          = "HP";
        $ins4['order_by']            = '4';
        $ins4['added_by']            = '1';
        $ins4['slug']                = Str::slug("HP");

        $check3 = Brands::where('brand_name', 'HP')->first();

        if( !$check3 ) {

            $data4 = Brands::create($ins4);
    
            $brand_id = $data4->id;
            $directory = 'brands/'.$brand_id;
            Storage::deleteDirectory('public/'.$directory);
            $imageName              = 'hp.jpg';
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/option1"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/option1"), 0775, true);
            }
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/default"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/default"), 0775, true);
            }
            $data4->brand_logo       = $imageName;
            $data4->update();
        }

        $ins5['brand_name']          = "LENOVO";
        $ins5['order_by']            = '5';
        $ins5['added_by']            = '1';
        $ins5['slug']                = Str::slug("LENOVO");

        $check4 = Brands::where('brand_name', 'LENOVO')->first();

        if( !$check4) {

            $data5 = Brands::create($ins4);
    
            $brand_id = $data5->id;
            $directory = 'brands/'.$brand_id;
            Storage::deleteDirectory('public/'.$directory);
            $imageName              = 'lenova.png';
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/option1"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/option1"), 0775, true);
            }
            if (!is_dir(storage_path("app/public/brands/".$brand_id."/default"))) {
                mkdir(storage_path("app/public/brands/".$brand_id."/default"), 0775, true);
            }
            $data5->brand_logo       = $imageName;
            $data5->update();
        }


    }
}
