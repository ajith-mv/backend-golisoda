<?php

namespace App\Http\Controllers;

use App\Models\GlobalSettings;
use App\Models\GlobalSiteLinks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Image;

class GlobalSettingController extends Controller
{
    public function index(Request $request)
    {
        $data = GlobalSettings::first();
        $breadCrum = array('Authentication', 'Global Settings');
        $title      = 'Global Settings';
        return view('platform.global.index',compact('data', 'breadCrum', 'title'));
    }

    public function getTab(Request $request)
    {
        
        $type = $request->tabType;
        $data = GlobalSettings::first();
        
        return view('platform.global._'.$type.'_form', compact('data') );
    }

    public function saveForm(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_mobile_number' => 'required|string',
            'site_email' => 'required|email',
            'cod_amount' => 'required_if:is_cod,==,on',
           
        ],[
            'cod_amount.required_if'=>'COD amount is required' ]);
        $id             = $request->id;

        if ($validator->passes()) {
           
            $ins['site_name'] = $request->site_name;
            $ins['site_mobile_no'] = $request->site_mobile_number;
            $ins['site_email'] = $request->site_email;
            $ins['copyrights'] = $request->copyrights;
            $ins['address'] = $request->address;
            $ins['is_pickup_from_store'] = ($request->is_pickup_from_store=="on")? 1 : 0;
            $ins['is_cod'] = ($request->is_cod=="on")? 1 : 0;
            $ins['cod_amount'] = $request->cod_amount?? NULL;
            if ($request->hasFile('favicon')) {
                $filename       = time() . '_' . $request->favicon->getClientOriginalName();
                $folder_name    = 'assets/global_setting/favicon/';

                if (!file_exists($folder_name)) {
                    mkdir($folder_name, 666, true);
                }
                $existID = '';
                if($id)
                {
                    $existID        = GlobalSettings::find($id);
                    $deleted_file   = $existID->favicon ?? '';
                    if( $deleted_file ) {
                        if( File::exists( $deleted_file ) ) {
                            File::delete( $deleted_file );
                        }
                    }
                }
                if (!file_exists($folder_name)) {
                    mkdir($folder_name, 666, true);
                }
                 //resize function start
                 $img = Image::make($request->favicon->getRealPath());
                 $img->resize(512, 512, function ($constraint) {
                     $constraint->aspectRatio();
                 })->save($folder_name.$filename);
                 //resize function end

                $path           = $folder_name.$filename;
                // $request->favicon->move(public_path($folder_name), $filename);
                $ins['favicon']   = $path;
            }
            if ($request->image_remove_favicon == "yes") {
                $ins['favicon'] = '';
            }

            if ($request->hasFile('logo')) {
                $filename       = time() . '_' . $request->logo->getClientOriginalName();
                $folder_name    = 'assets/global_setting/logo/';
                $existID = '';
                if($id)
                {
                    $existID = GlobalSettings::find($id);
                    $deleted_file = $existID->logo ?? '';
                    if( $deleted_file ) {
                        if(File::exists($deleted_file)) {
                            File::delete($deleted_file);
                        }
                    }
                    
                }
                if (!file_exists($folder_name)) {
                    mkdir($folder_name, 666, true);
                }
             
                $img1 = Image::make($request->logo->getRealPath());
                $img1->resize(200, 80, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($folder_name.$filename);
               
                $path           = $folder_name.$filename;
                // $request->logo->move(public_path($folder_name), $filename);
                $ins['logo']   = $path;
            }
            if ($request->image_remove_logo == "yes") {
                $ins['logo'] = '';
            }


            $error = 0;
            $info = GlobalSettings::updateOrCreate(['id' => $id],$ins);
            $message = (isset($id) && !empty($id)) ? 'Updated Successfully' :'Added successfully';
        } else {
            $error = 1;
            $message = $validator->errors()->all();
        }
        return response()->json(['error'=> $error, 'message' => $message]);
    }

    public function saveLinkForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link_name.*' => 'required',
            'link_url.*' => 'required|url',
           
        ]);
        $id             = 1;

        if ($validator->passes()) {
            
            $link_name  = $request->link_name;
            $link_url   = $request->link_url;
            $link_icon  = $request->link_icon;
            GlobalSiteLinks::where('site_id', $id)->delete();
            for ($i=0; $i < count($link_name); $i++) { 
                $ins['site_id']     = $id;
                $ins['link_name']   = $link_name[$i] ?? '' ;
                $ins['link_icon']   = $link_icon[$i] ?? '' ;
                $ins['link_url']    = $link_url[$i] ?? '' ;
                $ins['status']      = 'published';

                $info = GlobalSiteLinks::create($ins); 
            }
        
            $error = 0;
            $message = 'Updated Successfully';
        } else {
            $error = 1;
            $message = $validator->errors()->all();
        }
        return response()->json(['error'=> $error, 'message' => $message]);
    }
}
