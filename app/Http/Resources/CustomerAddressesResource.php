<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressesResource extends JsonResource
{
    
    public function toArray($request)
    {
        $response['customer_address_id'] = $this->id;
        $response['address_type'] = $this->subCategory->name;
        $response['address_type_id'] = $this->address_type_id;
        $response['name'] = $this->name;
        $response['email'] = $this->email;
        $response['mobile_no'] = $this->mobile_no;
        $response['address_line1'] = $this->address_line1;
        $response['address_line2'] = $this->address_line2;
        $response['landmark'] = $this->landmark;
        $response['countryid'] = $this->countryid;
        $response['country'] = $this->countries->name;
        $response['stateid'] = $this->stateid;
        $response['state'] = $this->states->state_name;
        $response['cityid'] = $this->cityid;
        $response['city'] = $this->city;
        $response['is_default'] = $this->is_default;
        if(isset($this->PostCode)){
              $response['post_code_number'] = $this->PostCode->pincode ?? '';
        }else{
              $response['post_code_number'] = '';
        }
        $response['post_code'] = $this->post_code;

        return $response;
    }
}
