<?php

namespace App\Imports;

use App\Models\Master\Pincode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PincodeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        /***
         * 1.check tax exist
         * 2.check category exist
         * 3.check subcategory exist
         * 4.check brand exist         
         */
           // dd($row);
        
        $postal_code           = $row['postal_code'];
        $delivery_text = $row['delivery_text'];
        $description = $row['description'];
        if( isset($postal_code) && !empty( $postal_code ) ) {

            $ins = [];
            $ins['pincode'] = $postal_code;
            $ins['shipping_information'] = $delivery_text ?? '';
            $ins['description'] = $description ?? '';
            $ins['status'] = 1;
            $ins['added_by'] = 1;
    
            Pincode::updateOrCreate(['pincode' => $postal_code], $ins);
        }
       
    }
}
