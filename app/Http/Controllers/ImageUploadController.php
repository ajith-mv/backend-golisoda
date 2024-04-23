<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Models\Product\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Image;

class ImageUploadController extends Controller
{
    public function index()
    {    
        $products=Product::all();
      
        $path = public_path('assets/GSS images');
        // $files = Storage::allFiles($directory);
        $files = File::allFiles($path);
        if( $files ) {
            foreach ($files as $item) {
                $file_name = $item->getFilename();
                $fileSize = $item->getSize();
                
                if( $file_name ){
                    $name = explode(' ', $file_name);
                   
                    $sku = current($name);
                    $image_order = end($name);
                    $image_order = current(explode( '.', $image_order ));
                  
                    if( isset( $image_order ) && !empty( $image_order ) ) {

                        $product_info = Product::where('sku', $image_order)->first();

                        if( isset( $product_info ) && !empty( $product_info ) ) {

                            $product_id = $product_info->id;

                            if( $image_order ) {

                                /** upload image */
                                $imageName                  = uniqid().str_replace(' ', '_', $file_name);
                                $directory                  = 'products/'.$product_id.'/default';
                                Storage::deleteDirectory('public/'.$directory);
    
                                if (!is_dir(storage_path("app/public/products/".$product_id."/default"))) {
                                    mkdir(storage_path("app/public/products/".$product_id."/default"), 0775, true);
                                }
    
                                $fileNameThumb              = 'public/products/'.$product_id.'/default/' .  $imageName;
                                Image::make($item)->save(storage_path('app/' . $fileNameThumb));
    
                                $product_info->base_image    = $fileNameThumb;
                                $product_info->update();
                            }

                            /**
                             * insert in product gallery
                             */
                             /** upload image */
                            $galleryImageName                  = uniqid().str_replace(' ', '_', $file_name);
                            //  $directory                  = 'products/'.$product_id.'/default';
                            //  Storage::deleteDirectory('public/'.$directory);

                            if (!is_dir(storage_path("app/public/products/".$product_id."/gallery"))) {
                                mkdir(storage_path("app/public/products/".$product_id."/gallery"), 0775, true);
                            }

                            $fileNameGallery              = 'public/products/'.$product_id.'/gallery/' .  $galleryImageName;
                            Image::make($item)->save(storage_path('app/' . $fileNameGallery));

                            $imageIns = array( 
                                'gallery_path'  => $fileNameGallery,                   
                                'product_id'    => $product_id,
                                'file_size'     => $fileSize,
                                'is_default'    => ($image_order == 1) ? "1": "0",
                                'order_by'      => $image_order,
                                'status'        => 'published'
                            );
            
                            //ProductImage::create($imageIns);
                        }
                    }
                }
            }
        }
       return redirect()->back()->with('success', 'Image upload successfully');
  
    }
    public function GalleryImage()
    {  
        $products=Product::all();
        foreach($products as $product){
    
            Storage::deleteDirectory("public/products/".$product->id."/gallery");
       
        }
        ProductImage::truncate();
        $path = public_path('assets/GSS Gallery Images');
    // $files = Storage::allFiles($directory);
        $files = File::allFiles($path);
        if( $files ) {
      
            foreach ($files as $item) {
                $file_name = $item->getFilename();
                $fileSize = $item->getSize();
    
                if( $file_name ){
                    $name = explode(' ', $file_name);
                    $sku = current($name);
                    $image_order = end($name);
                    $image_order = current(explode( '.', $image_order ));
                    $parts = explode('_', $image_order);
                    if(count($parts) > 1) {
                        $nextValue = $parts[1];
                    } else {
                        $nextValue = 0;
                    }
                    $image_order=preg_replace('/_(.)/', '', $image_order);
                    $product_info = Product::where('sku', $image_order)->first();
                    if( isset( $image_order ) && isset( $product_info ) ) {
    
                        $galleryImageName= uniqid().str_replace(' ', '_', $file_name);
                        $product_id=$product_info->id;
                        if (!is_dir(storage_path("app/public/products/".$product_id."/gallery"))) {
                            mkdir(storage_path("app/public/products/".$product_id."/gallery"), 0775, true);
                        }
    
                        $fileNameGallery              = 'public/products/'.$product_id.'/gallery/' .  $galleryImageName;
                         Image::make($item)->save(storage_path('app/' . $fileNameGallery));
    
                        $gallery_image=new ProductImage();
                        $gallery_image->gallery_path  = $fileNameGallery;  
                        $gallery_image->product_id=$product_id;
                        $gallery_image->file_size = $fileSize;
                        $gallery_image->is_default=0;
                        $gallery_image->order_by =$nextValue;
                        $gallery_image->status ='published'
                        ;
                        $gallery_image->save();
    
    
                    }
                }
            }
           
        }
        return redirect()->back()->with('success', 'Gallery Image upload successfully');
    
    }
    
}
