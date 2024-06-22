<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerAddressesResource;
use App\Mail\DynamicMail;
use App\Models\Cart;
use App\Models\Category\MainCategory;
use App\Models\GlobalSettings;
use App\Models\Master\Country;
use App\Models\Master\Customer;
use App\Models\Master\CustomerAddress;
use App\Models\Master\EmailTemplate;
use App\Models\Master\Pincode;
use App\Models\Master\State;
use App\Models\Product\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use Carbon\Carbon;
use Auth;

class CustomerController extends Controller
{
    public function verifyAccount(Request $request)
    {
        $email    = $request->token;
        $email    = base64_decode($email);
        $error    = 1;
        $message  = 'Token Expired';
        $customer = Customer::with('customerAddress')->where('email', $email)->whereNull('deleted_at')->first();
        if ($customer) {
            if (!empty($customer->verification_token)) {
                $customer->email_verified_at  = Carbon::now();
                $customer->verification_token = null;
                $customer->update();
                $error   = 0;
                $message = 'Account Verified Succesfully';
            }
        }

        return array('error' => $error, 'message' => $message, 'customer' => $customer);
    }

    public function registerCustomer(Request $request)
    {

        Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:customers,email,id,deleted_at,NULL',
            'password'   => 'required|string',

        ], ['email.unique' => 'Email id is already registered.Please try to login']);

        $customer = Customer::where('email', $request->email)->whereNull('deleted_at')->first();

        if (!$customer) {
            $ins['first_name']  = $request->first_name;
            $ins['last_name']   = $request->last_name ?? null;
            $ins['email']       = $request->email;
            $ins['mobile_no']   = $request->mobile ?? null;
            $ins['customer_no'] = getCustomerNo();
            $ins['password']    = Hash::make($request->password);
            $ins['status']      = 'published';
            $customer_data      = Customer::create($ins);
            $token_id           = base64_encode($request->email);

            /** send email for new customer */
            $emailTemplate = EmailTemplate::select('email_templates.*')
                ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                ->where('sub_categories.slug', 'new-registration')->first();

            // $link = 'http://192.168.0.35:2000/verify-account/' . $token_id;
            // $link = 'https://gbs-dev.vercel.app/verify-account/' . $token_id;
            // $link = 'https://beta.gbssystems.com/verify-account/' . $token_id;
            $globalInfo                        = GlobalSettings::first();
            $link                              = 'https://golisodastore.com/verify-account/' . $token_id;
            $customer_data->verification_token = $token_id;
            $customer_data->update();

            $extract = array(
                'name'              => $request->first_name . " " . $request->last_name,
                'regards'           => $globalInfo->site_name,
                'link'              => '<a href="' . $link . '"> Verify Account </a>',
                'company_website'   => '',
                'company_mobile_no' => $globalInfo->site_mobile_no,
                'company_address'   => $globalInfo->address
            );

            $templateMessage = $emailTemplate->message;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);
            extract($extract);
            eval("\$templateMessage = \"$templateMessage\";");

            try {

                $bccEmails = explode(',', env('EMAILS'));
                $bccRecipients = array_merge($bccEmails, [$request->email]);
                $send_mail = new DynamicMail($templateMessage, $emailTemplate->title);
                Mail::to($request->email)->bcc($bccRecipients)->send($send_mail);
            } catch (\Throwable $th) {
                throw $th;
                dd($th);
            }
            // return $send_mail->render();

            /** send sms for new customer */
            // if ($request->mobile_no) {

            //     $sms_params = array(
            //         'name'          => $request->firstName,
            //         'reference_id'  => $ins['customer_no'],
            //         'company_name'  => env('APP_NAME'),
            //         'login_details' => $ins['email'] . '/' . $request->password,
            //         'mobile_no'     => [$request->mobile_no]
            //     );

            //     sendGBSSms('register', $sms_params);
            // }

            $error   = 0;
            $message = 'Verification email is sent to your email address, Please verify account to login';
            $status  = 'success';
        } else {
            $error   = 1;
            $message = ['Email id is already exists'];
            $status  = 'error';
            // $message = $validator->errors()->all();
        }
        return array('error' => $error, 'message' => $message, 'status' => $status, 'customer' => $customer_data ?? '');
    }

    public function doLogin(Request $request)
    {

        $email         = $request->email;
        $password      = $request->password;
        $guest_token   = $request->guest_token;
        $checkCustomer = Customer::with(['customerAddress', 'customerAddress.subCategory'])->where('email', $email)->first();

        if ($checkCustomer) {
            if (Hash::check($password, $checkCustomer->password)) {
                $error = 0;
                $message = 'Login Success';
                $status = 'success';
                $customer_data = $checkCustomer;
                $customer_address = $checkCustomer->customerAddress ?? [];

                if ($guest_token) {

                    $cartData = Cart::where('guest_token', $guest_token)->get();
                    if (isset($cartData) && count($cartData) > 0) {
                        Cart::where('guest_token', $guest_token)->update(['guest_token' => null, 'customer_id' => $checkCustomer->id]);
                    }
                    $cartItems = Cart::where('customer_id', $checkCustomer->id)->get();


                    if ($cartItems->count() > 0) {
                        // Aggregate quantities for duplicate product-variation combinations
                        $aggregatedCart = [];
                        foreach ($cartItems as $item) {
                            $product_id = $item->product_id;
                            $variation_option_ids = [];
                    
                            if (isset($item->variationOptions) && !empty($item->variationOptions)) {
                                foreach ($item->variationOptions as $variationids) {
                                    $variation_option_ids[] = $variationids->variation_option_id;
                                }
                            }
                            // Sort the variation option IDs to ensure the key is consistent regardless of the order
                            sort($variation_option_ids);
                            $key = $product_id . '_' . implode('_', $variation_option_ids); // Unique key for each product-variation combination
                    
                            if (isset($aggregatedCart[$key])) {
                                $aggregatedCart[$key]['quantity'] += $item->quantity;
                                $aggregatedCart[$key]['ids'][] = $item->id;
                            } else {
                                $aggregatedCart[$key] = [
                                    'quantity' => $item->quantity,
                                    'ids' => [$item->id],
                                    'first_id' => $item->id,
                                    'other_ids' => [],
                                ];
                            }
                        }
                    
                        // Update quantities for the first occurrence and collect IDs for duplicates
                        foreach ($aggregatedCart as $key => $data) {
                            // Update the quantity of the first occurrence
                            Cart::where('id', $data['first_id'])->update(['quantity' => $data['quantity']]);
                    
                            // Collect other IDs for deletion
                            $data['other_ids'] = array_slice($data['ids'], 1);
                    
                            if (!empty($data['other_ids'])) {
                                // Remove duplicates
                                Cart::whereIn('id', $data['other_ids'])->delete();
                            }
                        }
                    }
                }
                $cart_count = Cart::where('customer_id', $checkCustomer->id)->get();
                $total_cart_count = count($cart_count);
            } else {
                $error = 1;
                $message = 'Invalid credentials';
                $status = 'error';
                $customer_data = '';
                $customer_address = [];
            }
        } else {
            $error = 1;
            $message = 'Invalid credentials';
            $status = 'error';
            $customer_data = '';
            $customer_address = [];
        }

        return array('error' => $error, 'message' => $message, 'status' => $status, 'customer' => $customer_data, 'customer_addres' => $customer_address, 'cart_count' => $cart_count ?? 0);
    }

    public function updateProfile(Request $request)
    {

        $customer_id  = $request->customer_id;
        $first_name   = $request->first_name;
        $last_name    = $request->last_name;
        $mobile_no    = $request->mobile_no;
        $customerInfo = Customer::find($customer_id);

        if ($first_name) {
            $customerInfo->first_name = $first_name;
        }
        if ($last_name) {
            $customerInfo->last_name = $last_name;
        }
        if ($mobile_no) {

            $customerInfo->mobile_no = $mobile_no;
        }
        if ($first_name || $last_name || $mobile_no) {

            $customerInfo->update();
        }
        return array('error' => 0, 'message' => 'Profile updated successfully', 'status' => 'success',  'customer' => $customerInfo);
    }

    public function changePassword(Request $request)
    {

        $customer_id      = $request->customer_id;
        $current_password = $request->current_password;
        $newPassword      = $request->password;
        $customerInfo     = Customer::find($customer_id);

        if ($customerInfo) {
            if ($current_password == $newPassword) {
                $error   = 1;
                $message = 'New password cannot be same as current password';
            } else if (isset($customerInfo) && !empty($customerInfo)) {

                if (Hash::check($current_password, $customerInfo->password)) {
                    $error                             = 0;
                    $customerInfo->password            = Hash::make($newPassword);
                    $customerInfo->password_changed_at = date('Y-m-d H:i:s');
                    $customerInfo->update();
                    $message = 'Password changed successfully';
                } else {
                    $error   = 1;
                    $message = 'Current password is not match';
                }
            }
        } else {
            $error   = 1;
            $message = 'Customer Data not exits';
        }


        return array('error' => $error, 'message' => $message);
    }



    public function sendPasswordLink(Request $request)
    {
        $email         = $request->email;
        $token_id      = base64_encode($email);
        $customer_info = Customer::where('email', $email)->first();

        if (isset($customer_info) && !empty($customer_info)) {

            $error                       = 0;
            $message                     = 'Password link sent to mail, Please check';
            $customer_info->forgot_token = $token_id;
            $customer_info->update();

            /** send email for new customer */
            $emailTemplate = EmailTemplate::select('email_templates.*')
                ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                ->where('sub_categories.slug', 'forgot-password')->first();

            $globalInfo = GlobalSettings::first();
            $link       = 'https://golisodastore.com/reset-password/' . $token_id;
            // $link = 'http://192.168.0.35:2000/verify-account/' . $token_id;
            // $link = 'https://gbs-dev.vercel.app/reset-password/' . $token_id;
            // $link = 'https://beta.gbssystems.com/reset-password/' . $token_id;

            $extract = array(
                'name'              => $customer_info->firstName . ' ' . $customer_info->last_name,
                'link'              => '<a href="' . $link . '"> Reset Password </a>',
                'regards'           => $globalInfo->site_name,
                'company_website'   => '',
                'company_mobile_no' => $globalInfo->site_mobile_no,
                'company_address'   => $globalInfo->address
            );

            $templateMessage = $emailTemplate->message;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);
            extract($extract);
            eval("\$templateMessage = \"$templateMessage\";");

            $send_mail = new DynamicMail($templateMessage, $emailTemplate->title);
            // return $send_mail->render();
            $bccEmails = explode(',', env('EMAILS'));
            $bccRecipients = array_merge($bccEmails, [$email]);
            Mail::to([$email])->bcc($bccRecipients)->send($send_mail);
        } else {
            $error   = 1;
            $message = 'Email id is not exists';
        }
        return array('error' => $error, 'message' => $message);
    }

    public function resetPasswordLink(Request $request)
    {
        $customer_id  = $request->customer_id;
        $password     = $request->password;
        $customerInfo = Customer::find($customer_id);

        if (isset($customerInfo) && !empty($customerInfo)) {

            $customerInfo->password            = Hash::make($password);
            $customerInfo->password_changed_at = date('Y-m-d H:i:s');
            $customerInfo->forgot_token        = null;
            $customerInfo->update();
            $error   = 0;
            $message = 'Password has been reset successfully. Please try login';
        } else {
            $error   = 1;
            $message = 'Customer not found, Please try register';
        }
        return array('error' => $error, 'message' => $message);
    }

    public function checkValidToken(Request $request)
    {
        $token_id     = $request->token_id;
        $customerInfo = Customer::where('forgot_token', $token_id)->first();

        if (isset($customerInfo) && !empty($customerInfo)) {
            $error   = 0;
            $message = 'Token is valid';
            $data    = $customerInfo;
        } else {
            $error   = 1;
            $message = 'Token is invalid';
            $data    = '';
        }
        return array('error' => $error, 'message' => $message, 'customer' => $data);
    }

    public function getProfileDetails(Request $request)
    {

        $customer_id   = $request->customer_id;
        $customer_info = Customer::find($customer_id);

        return array('status' => 'success', 'message' => 'Successfully fetched customer data', 'customer' => $customer_info);
    }

    public function getCustomerAddressDetails(Request $request)
    {

        $customer_id   = $request->customer_id;
        $address_array = $this->addressList($customer_id);
        $address_type  = MainCategory::with('subCategory')->where('slug', 'address-type')->first();
        $country       = Country::where('status', 1)->get();
        $state         = State::where('status', 1)->get();
        $pincode       = Pincode::where('status', 1)->orderBy('pincode')->get();

        $response = array(
            'status'       => 'success',
            'message'      => 'Successfully fetched address data',
            'addresses'    => $address_array,
            'address_type' => $address_type->subCategory,
            'country'      => $country,
            'state'        => $state,
            'pincode'      => $pincode
        );

        return $response;
    }

    public function addressList($customer_id)
    {
        $address_details = CustomerAddress::with(['countries', 'states', 'PostCode'])->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->get();
        return CustomerAddressesResource::collection($address_details);
    }

    public function addUpdateCustomerAddress(Request $request)
    {

        $ins['customer_id']     = $request->customer_id;
        $ins['address_type_id'] = $request->address_type_id;
        $ins['name']            = $request->name;
        $ins['email']           = $request->email;
        $ins['mobile_no']       = $request->mobile_no;
        $ins['address_line1']   = $request->address;
        $ins['countryid']       = $request->country;
        $ins['stateid']         = $request->state;
        $pincode = Pincode::where('pincode', $request->post_code)->first();
        if (isset($pincode)) {
            $ins['post_code']       = $pincode->id;
        } else {
            $pin = new Pincode();
            $pin->pincode = $request->post_code;
            $pin->description = 'new';
            $pin->shipping_information = 'Standard Delivery';
            $pin->added_by = Auth::id();
            $pin->status = 1;
            $pin->save();
            $ins['post_code']       = $pin->id;
        }
        $ins['city']            = $request->city;

        CustomerAddress::updateOrCreate(['id' => $request->id], $ins);

        $response = array(
            'error'     => 0,
            'message'   => $request->id ? 'Address Updated successfully' : 'Address Added successfully',
            'status'    => 'success',
            'addresses' => $this->addressList($request->customer_id)
        );
        return $response;
    }

    public function setDefaultAddress(Request $request)
    {

        $customer_address_id = $request->customer_address_id;

        if (!$customer_address_id) {
            $error   = 1;
            $message = 'Address id is required';
        } else {
            $error   = 0;
            $message = 'Address set as Default Successfully';
            CustomerAddress::where('customer_id', $request->customer_id)->update(['is_default' => 0]);

            $address = CustomerAddress::find($customer_address_id);
            $address->is_default = 1;
            $address->update();
        }

        return array('error' => $error, 'message' => $message, 'addresses' =>  $this->addressList($request->customer_id));
    }

    public function deleteCustomerAddress(Request $request)
    {

        $address_id = $request->customer_address_id;
        if (!$address_id) {
            $error   = 1;
            $message = 'Address not found';
        } else {
            $error       = 0;
            $message     = 'Address deleted successfully';
            $addressInfo = CustomerAddress::find($address_id);
            if ($addressInfo) {
                $addressInfo->delete();
            }
        }

        return array('error' => $error, 'message' => $message, 'addresses' =>  $this->addressList($request->customer_id));
    }

    public function setWishlists(Request $request)
    {
        $customer_id        = $request->customer_id;
        $product_id         = $request->product_id;
        $status             = $request->status;
        $ins['customer_id'] = $customer_id;
        $ins['product_id']  = $product_id;
        $ins['status']      = $status;

        Wishlist::updateOrCreate(['customer_id' => $customer_id, 'product_id' => $product_id], $ins);

        if ($status == 1) {
            $message = 'Added to Wishlist';
        } else {
            $message = 'Removed from Wishlist';
        }
        return array('error' => 0, 'message' => $message);
    }

    public function clearWishlist(Request $request)
    {
        $customer_id = $request->customer_id;
        Wishlist::where(['customer_id' => $customer_id])->delete();
        return array('error' => 0, 'message' => 'Cleared Wishlist Successfully');
    }

    public function getWishlist(Request $request)
    {
        $customer_id = $request->customer_id;
        $wishlist    = Wishlist::where('status', 1)->where(['customer_id' => $customer_id])->get();

        $wishlist_arr = [];
        if (isset($wishlist) && !empty($wishlist)) {
            foreach ($wishlist as $items) {
                $product_data   = Product::find($items->product_id);
                $wishlist_arr[] = getProductApiData($product_data, $customer_id);
            }
        }

        return $wishlist_arr;
    }
}
