<?php

namespace App\Http\Controllers;

use App\Exports\StoreLocatorExport;
use App\Models\Master\Brands;
use App\Models\StoreLocator;
use App\Models\StoreLocatorBrand;
use App\Models\StoreLocatorContact;
use App\Models\StoreLocatorEmail;
use App\Models\StoreLocatorMetaTag;
use App\Models\StoreLocatorPincode;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

class StoreLocatorController extends Controller
{
    public function index(Request $request)
    {
        $title = "Store Locator";
        $breadCrum = array("Store Locator", "Store Locator");
        if ($request->ajax()) {

            $data = StoreLocator::select('store_locators.*', 'brands.brand_name as brand_name', 'users.name as user_name')
                ->join('users', 'users.id', '=', 'store_locators.added_by')
                ->leftJoin('brands', 'brands.id', '=', 'store_locators.brand_id');

            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($status, $keywords) {
                    return $query->when($status != '', function ($q) use ($status) {
                        $q->where('store_locators.status', '=', $status);
                    })->when($keywords != '', function ($q) use ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $q->where('store_locators.title', 'like', "%{$keywords}%")
                            ->orWhere('brands.brand_name', 'like', "%{$keywords}%");
                    });
                })
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-' . (($row->status == 'published') ? 'success' : 'danger') . '" tooltip="Click to ' . (($row->status == 'published') ? 'Unpublish' : 'Publish') . '" onclick="return commonChangeStatus(' . $row->id . ', \'' . (($row->status == 'published') ? 'unpublished' : 'published') . '\', \'store-locator\')">' . ucfirst($row->status) . '</a>';
                    return $status;
                })
                ->addColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'store-locator\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                <i class="fa fa-edit"></i>
            </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'store-locator\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
            <i class="fa fa-trash"></i></a>';
                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }
        return view('platform.store_locator.index', compact('title', 'breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {

        $title              = "Add Store Locator";
        $breadCrum          = array('Store Locator', 'Add Store Locator');

        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Store Locator';
        $brands    = Brands::where('status', 'published')->get();
        $usedBrands = [];
        if (isset($id) && !empty($id)) {
            $info           = StoreLocator::find($id);
            $modal_title    = 'Update Store Locator';
            if (isset($info->brands) && !empty($info->brands)) {
                $usedBrands = array_column($info->brands->toArray(), 'brand_id');
            }
        }

        return view('platform.store_locator.form.add_edit_modal', compact('modal_title', 'breadCrum', 'info', 'brands', 'usedBrands'));
    }

    public function saveForm(Request $request)
    {
        $id             = $request->id;
        $slug             = $request->slug;
        $title             = $request->title;
        $parent_id      = $request->parent_location;
        $validator      = Validator::make($request->all(), [
            'title' => [
                'required', 'string',
                Rule::unique('store_locators')->where(function ($query) use ($id, $parent_id, $title) {
                    return $query->where('parent_id', $parent_id)
                    ->where('title', $title)
                        ->where('deleted_at', NULL)->when($id != '', function ($q) use ($id) {
                        return $q->where('id', '!=', $id);
                    });
                }),
            ],
            'slug' => [
                'required', 'string',
                Rule::unique('store_locators')->where(function ($query) use ($id, $slug) {
                    return $query->where('slug', $slug)->where('deleted_at', NULL)->when($id != '', function ($q) use ($id) {
                        return $q->where('id', '!=', $id);
                    });
                }),
            ],
            'brand_id' => 'required',
            'description' => 'required',
            'whatsapp_no' => 'required',
        ]);
        $serviceCenterId         = '';

        if ($validator->passes()) {

            $contact = isset($request->contact) && !empty( $request->contact ) ? array_filter($request->contact) : [];
            $email = isset( $request->email ) && !empty( $request->email ) ? array_filter($request->email) : [];
            $near_pincode = isset($request->near_pincode) && !empty( $request->near_pincode ) ? array_filter($request->near_pincode) : [];
            $brand_id = $request->brand_id;

            if (!$request->is_parent) {
                $parent_slug = StoreLocator::where('id', $parent_id)->select('slug')->first();
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
            $ins['address'] = $request->address;
            $ins['whatsapp_no'] = $request->whatsapp_no;
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
            $serviceCenterInfo          = StoreLocator::updateOrCreate(['id' => $id], $ins);

            $store_locator_id            = $serviceCenterInfo->id;

            if (isset($near_pincode) && !empty($near_pincode)) {
                StoreLocatorPincode::where('store_locator_id', $store_locator_id)->delete();

                foreach ($near_pincode as $key => $val) {
                    $ins['store_locator_id']       = $store_locator_id;
                    $ins['pincode']                 = $val;
                    $ins['status']                  = Auth::id();
                    StoreLocatorPincode::create($ins);
                }
            }

            if (isset($contact) && !empty($contact)) {
                StoreLocatorContact::where('store_locator_id', $store_locator_id)->delete();

                foreach ($contact as $key => $val) {
                    $ins['store_locator_id']       = $store_locator_id;
                    $ins['contact']                 = $val;
                    $ins['status']                  = Auth::id();
                    StoreLocatorContact::create($ins);
                }
            }

            if (isset($email) && !empty($email)) {
                StoreLocatorEmail::where('store_locator_id', $store_locator_id)->delete();

                foreach ($email as $key => $val) {
                    $ins['store_locator_id']       = $store_locator_id;
                    $ins['email']                   = $val;
                    $ins['status']                  = Auth::id();
                    $data = StoreLocatorEmail::create($ins);
                }
            }

            if ($request->hasFile('banner')) {

                $imagName               = time() . '_' . $request->banner->getClientOriginalName();
                $directory              = 'store/' . $store_locator_id . '/banner';
                Storage::deleteDirectory('public/' . $directory);

                if (!is_dir(storage_path("app/public/store/" . $store_locator_id . "/banner"))) {
                    mkdir(storage_path("app/public/store/" . $store_locator_id . "/banner"), 0775, true);
                }

                $thumbnailPath          = 'public/store/' . $store_locator_id . '/banner/' . $imagName;
                Image::make($request->file('banner'))->save(storage_path('app/' . $thumbnailPath));

                $serviceCenterInfo->banner    = $thumbnailPath;
                $serviceCenterInfo->save();
            }

            if ($request->hasFile('banner_mb')) {

                $imagName               = time() . '_' . $request->banner_mb->getClientOriginalName();
                $directory              = 'store/' . $store_locator_id . '/banner_mb';
                Storage::deleteDirectory('public/' . $directory);

                if (!is_dir(storage_path("app/public/store/" . $store_locator_id . "/banner_mb"))) {
                    mkdir(storage_path("app/public/store/" . $store_locator_id . "/banner_mb"), 0775, true);
                }

                $bannerPath             = 'public/store/' . $store_locator_id . '/banner_mb/' . $imagName;
                Image::make($request->file('banner_mb'))->save(storage_path('app/' . $bannerPath));

                $serviceCenterInfo->banner_mb    = $bannerPath;
                $serviceCenterInfo->save();
            }

            $meta_title = $request->meta_title;
            $meta_keywords = $request->meta_keywords;
            $meta_description = $request->meta_description;
            if (!empty($meta_title) || !empty($meta_keywords) || !empty($meta_description)) {
                StoreLocatorMetaTag::where('store_locator_id', $store_locator_id)->delete();
                $metaIns['meta_title']          = $meta_title;
                $metaIns['meta_keyword']       = $meta_keywords;
                $metaIns['meta_description']    = $meta_description;
                $metaIns['store_locator_id']         = $store_locator_id;
                StoreLocatorMetaTag::create($metaIns);
            }

            /**
             * insert multi brand here
             */
            if (isset($brand_id) && !empty($brand_id)) {
                StoreLocatorBrand::where('store_locator_id', $store_locator_id)->update(['status' => 'inactive']);
                foreach ($brand_id as $item) {
                    $ins = [];
                    $ins['store_locator_id'] = $store_locator_id;
                    $ins['brand_id'] = $item;
                    $ins['status'] = 'active';
                    StoreLocatorBrand::create($ins);
                }
            }

            $message = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
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
        $info           = StoreLocator::find($id);
        $info->status   = $status;
        $info->update();

        return response()->json(['message' => "You changed the status!", 'status' => 1]);
    }

    public function delete(Request $request)
    {

        $id         = $request->id;
        $info       = StoreLocator::find($id);
        $info->delete();
        return response()->json(['message' => "Successfully deleted!", 'status' => 1]);
    }

    public function export()
    {
        return Excel::download(new StoreLocatorExport, 'store_locator.xlsx');
    }

    public function exportPdf()
    {
        $list       = StoreLocator::all();
        $pdf        = PDF::loadView('platform.exports.product.product_category_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('productCategories.pdf');
    }
}
