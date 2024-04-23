<?php

namespace Database\Seeders;

use App\Models\Category\SubCategory;
use App\Models\Master\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $logo = asset('assets/logo/logo.webp');
        
        $type = SubCategory::where('slug', 'new-registration')->first();
        if( isset( $type ) && !empty( $type ) ) {

            $ins['type_id'] = $type->id;
            $ins['title'] = 'GBS System & Service Private Limited';
            $ins['message'] = '<p><img src="'.$logo.'" alt=\"Logo\"></p><p><br></p><p><br></p><p>Hi {$name},</p><p><br></p><p>GBS System & Service Private Limited online purchase website.</p><p>Complete your registration {$link}<br></p><p><br></p><p>Regards,</p><p>{$regards}.</p><p><br></p><p>{$company_website}</p><p>{$company_mobile_no}</p><p>{$company_address}</p><p><br></p>';
            $ins['params'] = 'name,regards,company_website,company_mobile_no,company_address,link';
            $ins['status'] = 'published';

            EmailTemplate::create($ins);
        }

        $type = SubCategory::where('slug', 'new-order')->first();
        if( isset( $type ) && !empty( $type ) ) {

            $ins1['type_id']    = $type->id;
            $ins1['title']      = 'New Order Has been received: {$order_id}';
            $ins1['message']    = '<p><img src="'.$logo.'" alt=\"Logo\"></p><p><br></p><p><br></p><p>Hi {$name},</p><p><br></p><p>We\'ve got your orders!. Your world is about to look a whole lot better.</p><p>We\'ll drop you another email when your order ships</p><p><br></p><p>{$dynamic_content}</p><p><br></p><p><br></p><p>Regards,</p><p>{$regards}.</p><p><br></p><p>{$company_website}</p><p>{$company_mobile_no}</p><p>{$company_address}</p><p><br></p>';
            $ins1['params']     = 'order_id,name,dynamic_content,regards,company_website,company_mobile_no,company_address';
            $ins1['status']     = 'published';

            EmailTemplate::create($ins1);
        }

        $type = SubCategory::where('slug', 'order-cancelled')->first();
        if( isset( $type ) && !empty( $type ) ) {
            
            $ins2['type_id']    = $type->id;
            $ins2['title']      = 'Your Order Has been cancelled';
            $ins2['message']    = '<p><img src="'.$logo.'" alt=\"Logo\"></p><p><br></p><p><br></p><p>Hi {$name},</p><p><br></p><p>We are sorry, Your order has been cancelled</p><p>{$cancel_reason}</p><p><br></p><p>{$dynamic_content}</p><p><br></p><p><br></p><p>Regards,</p><p>{$regards}.</p><p><br></p><p>{$company_website}</p><p>{$company_mobile_no}</p><p>{$company_address}</p><p><br></p>';
            $ins2['params']     = 'name,cancel_reason,dynamic_content,regards,company_website,company_mobile_no,company_address';
            $ins2['status']     = 'published';

            EmailTemplate::create($ins2);
        }

        $type = SubCategory::where('slug', 'order-shipped')->first();
        if( isset( $type ) && !empty( $type ) ) {
            
            $ins3['type_id']    = $type->id;
            $ins3['title']      = 'Order Shipped - OrderNo:{$order_no}';
            $ins3['message']    = '<p><p><img src="'.$logo.'" alt=\"Logo\"></p><p><br></p><p><br></p><p>Hi {$name},</p><p><br></p><p>Your Order Has been shipped.</p><p>You can track your order on your account profile : {$customer_login_url}</p><p><br></p><p><br></p><p>Regards,</p><p>{$regards}.</p><p><br></p><p>{$company_website}</p><p>{$company_mobile_no}</p><p>{$company_address}</p><p><br></p>';
            $ins3['params']     = 'customer_login_url,name,regards,company_website,company_mobile_no,company_address,order_no';
            $ins3['status']     = 'published';

            EmailTemplate::create($ins3);
        }

        $type = SubCategory::where('slug', 'order-delivered')->first();
        if( isset( $type ) && !empty( $type ) ) {
            
            $ins4['type_id']    = $type->id;
            $ins4['title']      = 'Order {$order_id} successfully delivered';
            $ins4['message']    = '<p><img src="'.$logo.'" alt=\"Logo\"></p><p><br></p><p><br></p><p>Hi {$name},</p><p><br></p><p>Your Order Has been delivered successfully.</p><p>You can track your order on your account profile : {$customer_login_url}</p><p><br></p><p><br></p><p>Regards,</p><p>{$regards}.</p><p><br></p><p>{$company_website}</p><p>{$company_mobile_no}</p><p>{$company_address}</p><p><br></p>';
            $ins4['params']     = 'order_id,name,customer_login_url,regards,company_website,company_mobile_no,company_address';
            $ins4['status']     = 'published';

            EmailTemplate::create($ins4);
        }

        $type = SubCategory::where('slug', 'forgot-password')->first();
        if( isset( $type ) && !empty( $type ) ) {
            
            $ins5['type_id']    = $type->id;
            $ins5['title']      = 'Password Reset Link';
            $ins5['message']    = '<p><img src="'.$logo.'" alt=\"Logo\"></p><p><br></p><p><br></p><p>Hi {$name},</p><p><br></p><p>You can reset you password by clicking link: {$link}</p><p><br></p><p><br></p><p>Regards,</p><p>{$regards}.</p><p><br></p><p>{$company_website}</p><p>{$company_mobile_no}</p><p>{$company_address}</p><p><br></p>';
            $ins5['params']     = 'name,link,regards,company_website,company_mobile_no,company_address';
            $ins5['status']     = 'published';

            EmailTemplate::create($ins5);
        }

        $type = SubCategory::where('slug', 'order-cancel-requested')->first();
        if( isset( $type ) && !empty( $type ) ) {
            
            $ins5['type_id']    = $type->id;
            $ins5['title']      = 'You have received Order Cancel Requested';
            $ins5['message']    = '<p><img src="'.$logo.'" alt=\"Logo\"></p><p><br></p><p><br></p><p>You have got orders cancel request!. </p><p><br></p><p>{$dynamic_content}</p><p>{$cancel_reason}</p><p><br></p><p><br></p><p>Regards,</p><p>{$regards}.</p><p><br></p><p>{$company_website}</p><p>{$company_mobile_no}</p><p>{$company_address}</p><p><br></p>';
            $ins5['params']     = 'dynamic_content,cancel_reason,regards,company_website,company_mobile_no,company_address';
            $ins5['status']     = 'published';

            EmailTemplate::create($ins5);
        }
    }
}
