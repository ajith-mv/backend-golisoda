<?php

namespace App\Imports;

use App\Models\Product\ProductCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TestImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $checkins = [
            "name" => "Keyboard",
            "parent_id" => 0,
            "description" => null,
            "status" => "published",
            "is_featured" => "0",
            "added_by" => 1,
            "tag_line" => null,
            "tax_id" => 1,
            "is_home_menu" => "no",
        ];
        $categoryInfo       = \DB::table('product_categories')->insert($checkins);     
    }
}
