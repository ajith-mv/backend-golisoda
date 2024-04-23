<?php

namespace Database\Seeders;

use App\Models\Category\MainCategory;
use App\Models\Category\SubCategory;
use Illuminate\Database\Seeder;

class EmailTemplateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subCat             = MainCategory::where('slug', 'email-template')->where('status',"published")->first();
        if( isset( $subCat ) && !empty( $subCat ) ) {
            $category_id = $subCat->id;
        } else {
            $ins = [];
            $ins['category_name'] = 'Email Template';
            $ins['slug'] = 'email-template';
            $ins['order_by'] = '0';
            $ins['status'] = 'published';
            $ins['added_by'] = '1';

            $category_id = MainCategory::create($ins);
        }

        $par['parent_id'] = $category_id;
        $par['name'] = 'New Registration';
        $par['slug'] = 'new-registration';
        $par['order_by'] = '1';
        $par['status'] = 'published';
        $par['added_by'] = '1';

        SubCategory::create($par);

        $par1['parent_id'] = $category_id;
        $par1['name'] = 'New Order';
        $par1['slug'] = 'new-order';
        $par1['order_by'] = '2';
        $par1['status'] = 'published';
        $par1['added_by'] = '1';

        SubCategory::create($par1);

        $par2['parent_id'] = $category_id;
        $par2['name'] = 'Order Cancelled';
        $par2['slug'] = 'order-cancelled';
        $par2['order_by'] = '3';
        $par2['status'] = 'published';
        $par2['added_by'] = '1';

        SubCategory::create($par2);

        $par3['parent_id'] = $category_id;
        $par3['name'] = 'Order Shipped';
        $par3['slug'] = 'order-shipped';
        $par3['order_by'] = '4';
        $par3['status'] = 'published';
        $par3['added_by'] = '1';

        SubCategory::create($par3);

        $par4['parent_id'] = $category_id;
        $par4['name'] = 'Order Delivered';
        $par4['slug'] = 'order-delivered';
        $par4['order_by'] = '5';
        $par4['status'] = 'published';
        $par4['added_by'] = '1';

        SubCategory::create($par4);

        $par4['parent_id'] = $category_id;
        $par4['name'] = 'Forgot Password';
        $par4['slug'] = 'forgot-password';
        $par4['order_by'] = '6';
        $par4['status'] = 'published';
        $par4['added_by'] = '1';

        SubCategory::create($par4);

        $par4['parent_id'] = $category_id;
        $par4['name'] = 'Order Cancel Requested';
        $par4['slug'] = 'order-cancel-requested';
        $par4['order_by'] = '7';
        $par4['status'] = 'published';
        $par4['added_by'] = '1';

        SubCategory::create($par4);

    }
}
