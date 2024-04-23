<?php

namespace App\Http\Controllers;

use App\Exports\Export;
use App\Exports\ServiceCenterExport;
use App\Models\Master\Brands;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterBrand;
use App\Models\ServiceCenterContact;
use App\Models\ServiceCenterEmail;
use App\Models\ServiceCenterMetaTag;
use App\Models\ServiceCenterPincode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Auth;
use Excel;
use PDF;
use Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class ServiceCenterController extends Controller
{
    public function index(Request $request)
    {
        $title = "Service Center";
        $breadCrum = array("Service Center", "Service Center");
        if ($request->ajax()) {

            $data = ServiceCenter::select(
                'service_centers.*',
                'users.name as user_name',
                DB::raw('IF(gbs_service_centers.parent_id = 0,"Parent",gbs_parent_category.title) as parent_name')
            )
                ->join('users', 'users.id', '=', 'service_centers.added_by')
                ->leftJoin('service_centers as parent_category', 'parent_category.id', '=', 'service_centers.parent_id');
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($status, $keywords) {
                    return $query->when($status != '', function ($q) use ($status) {
                        $q->where('service_centers.status', '=', $status);
                    })->when($keywords != '', function ($q) use ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $q->where('service_centers.title', 'like', "%{$keywords}%")
                            ->orWhere('service_centers.description', 'like', "%{$keywords}%")
                            ->orWhereDate("service_centers.created_at", $date);
                    });
                })
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-' . (($row->status == 'published') ? 'success' : 'danger') . '" tooltip="Click to ' . (($row->status == 'published') ? 'Unpublish' : 'Publish') . '" onclick="return commonChangeStatus(' . $row->id . ', \'' . (($row->status == 'published') ? 'unpublished' : 'published') . '\', \'service-center\')">' . ucfirst($row->status) . '</a>';
                    return $status;
                })
                ->addColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'service-center\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                <i class="fa fa-edit"></i>
            </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'service-center\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
            <i class="fa fa-trash"></i></a>';
                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }

        return view('platform.service_center.index', compact('title', 'breadCrum'));
    }
    public function modalAddEdit(Request $request)
    {

        $title              = "Add Service Center";
        $breadCrum          = array('Products', 'Add Service Center');

        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Service Center';
        $brands             = Brands::where('status', 'published')->get();
        $serviceCenter    = ServiceCenter::where('status', 'published')->where('parent_id', 0)->get();
        $usedBrands = [];
        if (isset($id) && !empty($id)) {
            $info           = ServiceCenter::find($id);
            $modal_title    = 'Update Service Center';
            if (isset($info->brands) && !empty($info->brands)) {
                $usedBrands = array_column($info->brands->toArray(), 'brand_id');
            }
        }
        return view('platform.service_center.form.add_edit_modal', compact('modal_title', 'breadCrum', 'info', 'serviceCenter', 'brands', 'usedBrands'));
    }

    public function saveForm(Request $request)
    {

        $id             = $request->id;
        $slug           = $request->slug;
        $title          = $request->title;
        $parent_id      = $request->parent_location;
        $validator      = Validator::make($request->all(), [
            'title' => [
                'required', 'string',
                Rule::unique('service_centers')->where(function ($query) use ($id, $parent_id, $title) {
                    return $query->where('parent_id', $parent_id)
                            ->where('title', $title)
                            ->where('deleted_at', NULL)->when($id != '', function ($q) use ($id) {
                        return $q->where('id', '!=', $id);
                    });
                }),
            ],
            'slug' => [
                'required', 'string',
                Rule::unique('service_centers')->where(function ($query) use ($id, $parent_id, $slug) {
                    return $query
                        ->where('slug', $slug )
                        ->where('deleted_at', NULL)->when($id != '', function ($q) use ($id) {
                        return $q->where('id', '!=', $id);
                    });
                }),
            ],
            'brand_id' => 'required',
            'description' => 'required',
            'banner'      => 'max:150',
            'banner_mb' => 'max:150',
            'whatsapp_no' => 'required',
        ]);
        $serviceCenterId         = '';

        if ($validator->passes()) {

            $contact = isset($request->contact) && !empty( $request->contact ) ? array_filter($request->contact) : [];
            $email = isset( $request->email ) && !empty( $request->email ) ? array_filter($request->email) : [];
            $near_pincode = isset($request->near_pincode) && !empty( $request->near_pincode ) ? array_filter($request->near_pincode) : [];
            $brand_id = $request->brand_id;

            if (!$request->is_parent) {
                $parent_slug = ServiceCenter::where('id', $parent_id)->select('slug')->first();
                $parent_slug = $parent_slug->slug ?? '';
                $ins['slug'] = $request->slug ?? ($parent_slug . '-' . \Str::slug($request->title));
                $ins['parent_id'] = $request->parent_location;
            } else {

                $ins['slug'] = $request->slug ?? (\Str::slug($request->title));
                $ins['parent_id'] = 0;
            }
            if (!$id) {
                $ins['added_by'] = Auth::id();
            } else {
                $ins['added_by'] = Auth::id();
            }
            $ins['title'] = $request->title;
            $ins['whatsapp_no'] = $request->whatsapp_no;
            $ins['address'] = $request->address;
            $ins['pincode'] = $request->pincode;
            $ins['description'] = $request->description;
            $ins['map_link'] = $request->map_link;
            $ins['image_360_link'] = $request->image_360_link;
            $ins['order_by'] = $request->order_by ?? 0;

            if ($request->status) {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                      = 0;
            $serviceCenterInfo          = ServiceCenter::updateOrCreate(['id' => $id], $ins);

            $serviceCenterId            = $serviceCenterInfo->id;

            if (isset($near_pincode) && !empty($near_pincode)) {
                ServiceCenterPincode::where('service_center_id', $serviceCenterId)->delete();

                foreach ($near_pincode as $key => $val) {
                    $ins['service_center_id']       = $serviceCenterId;
                    $ins['pincode']                 = $val;
                    $ins['status']                  = Auth::id();
                    ServiceCenterPincode::create($ins);
                }
            }

            if (isset($contact) && !empty($contact)) {
                ServiceCenterContact::where('service_center_id', $serviceCenterId)->delete();

                foreach ($contact as $key => $val) {
                    $ins['service_center_id']       = $serviceCenterId;
                    $ins['contact']                 = $val;
                    $ins['status']                  = Auth::id();
                    ServiceCenterContact::create($ins);
                }
            }

            if (isset($email) && !empty($email)) {
                ServiceCenterEmail::where('service_center_id', $serviceCenterId)->delete();

                foreach ($email as $key => $val) {
                    $ins['service_center_id']       = $serviceCenterId;
                    $ins['email']                   = $val;
                    $ins['status']                  = Auth::id();
                    $data = ServiceCenterEmail::create($ins);
                }
            }

            if ($request->hasFile('banner')) {

                $imagName               = time() . '_' . $request->banner->getClientOriginalName();
                $directory              = 'serviceCenter/' . $serviceCenterId . '/banner';
                Storage::deleteDirectory('public/' . $directory);

                if (!is_dir(storage_path("app/public/serviceCenter/" . $serviceCenterId . "/banner"))) {
                    mkdir(storage_path("app/public/serviceCenter/" . $serviceCenterId . "/banner"), 0775, true);
                }

                $thumbnailPath          = 'public/serviceCenter/' . $serviceCenterId . '/banner/' . $imagName;
                Image::make($request->file('banner'))->save(storage_path('app/' . $thumbnailPath));

                $serviceCenterInfo->banner    = $thumbnailPath;
                $serviceCenterInfo->save();
            }

            if ($request->hasFile('banner_mb')) {

                $imagName               = time() . '_' . $request->banner_mb->getClientOriginalName();
                $directory              = 'serviceCenter/' . $serviceCenterId . '/banner_mb';
                Storage::deleteDirectory('public/' . $directory);

                if (!is_dir(storage_path("app/public/serviceCenter/" . $serviceCenterId . "/banner_mb"))) {
                    mkdir(storage_path("app/public/serviceCenter/" . $serviceCenterId . "/banner_mb"), 0775, true);
                }

                $bannerPath             = 'public/serviceCenter/' . $serviceCenterId . '/banner_mb/' . $imagName;
                Image::make($request->file('banner_mb'))->save(storage_path('app/' . $bannerPath));

                $serviceCenterInfo->banner_mb    = $bannerPath;
                $serviceCenterInfo->save();
            }

            $meta_title = $request->meta_title;
            $meta_keywords = $request->meta_keywords;
            $meta_description = $request->meta_description;
            if (!empty($meta_title) || !empty($meta_keywords) || !empty($meta_description)) {
                ServiceCenterMetaTag::where('service_center_id', $serviceCenterId)->delete();
                $metaIns['meta_title']          = $meta_title;
                $metaIns['meta_keyword']       = $meta_keywords;
                $metaIns['meta_description']    = $meta_description;
                $metaIns['service_center_id']         = $serviceCenterId;
                ServiceCenterMetaTag::create($metaIns);
            }

            /**
             * insert multi brand here
             */
            if (isset($brand_id) && !empty($brand_id)) {
                ServiceCenterBrand::where('service_center_id', $serviceCenterId)->update(['status' => 'inactive']);
                foreach ($brand_id as $item) {
                    $ins = [];
                    $ins['service_center_id'] = $serviceCenterId;
                    $ins['brand_id'] = $item;
                    $ins['status'] = 'active';
                    ServiceCenterBrand::create($ins);
                }
            }

            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'serviceCenterId' => $serviceCenterId]);
    }

    public function changeStatus(Request $request)
    {

        $id             = $request->id;
        $status         = $request->status;
        $info           = ServiceCenter::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message' => "You changed the status!", 'status' => 1]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = ServiceCenter::find($id);
        $info->delete();
        // $directory      = 'serviceCenter/banner/'.$id;
        // Storage::deleteDirectory($directory);
        // $directory      = 'serviceCenter/banner_mb/'.$id;
        // Storage::deleteDirectory($directory);
        // $directory      = 'serviceCenter/'.$id;
        // Storage::deleteDirectory($directory);
        // echo 1;
        return response()->json(['message' => "Successfully deleted!", 'status' => 1]);
    }

    public function export()
    {
        return Excel::download(new ServiceCenterExport, 'service_center.xlsx');
    }

    public function exportPdf()
    {
        $list       = ServiceCenter::all();
        $pdf        = PDF::loadView('platform.exports.product.product_category_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('productCategories.pdf');
    }
}
