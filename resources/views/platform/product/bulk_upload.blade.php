@extends('platform.layouts.template')
@section('toolbar')
    <style>
        .content {
            padding: 10px 0;
        }
        .button {
  position: relative;
  padding: 8px 16px;
  border: none;
  outline: none;
  border-radius: 2px;
  cursor: pointer;
}



.button__text {
  font: bold 20px "Quicksand", san-serif;
  color: #ffffff;
  transition: all 0.2s;
}

.button--loading .button__text {
  visibility: hidden;
  opacity: 0;
}

.button--loading::after {
  content: "";
  position: absolute;
  width: 16px;
  height: 16px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
  border: 4px solid transparent;
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: button-loading-spinner 1s ease infinite;
}

@keyframes button-loading-spinner {
  from {
    transform: rotate(0turn);
  }

  to {
    transform: rotate(1turn);
  }
}

    </style>
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            @include('platform.layouts.parts._breadcrum')
            @include('platform.layouts.parts._menu_add_button')
        </div>
    </div>
@endsection
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card">
            <div class="card-body py-4">
                <div class="row mb-2">
                    <div class="col-sm-12 text-start">
                        <div class="row">
                            <div class="col-8">
                                <h3> Product Upload</h3>
                                <form id="importform" method="POST" action="{{ route('products.bulk.upload') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label">Select Import File</label>
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-3 pt-5">
                                            <button type="submit" class="btn btn-primary mb-2">Import</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-4">
                                <label for=""> Sample Excel file </label>
                                <div class="mt-2">
                                    <a href="{{ asset('assets/data/product_masters.xlsx') }}"> <i
                                            class="mdi mdi-file h2"></i> Download Sample</a>
                                </div>
                            </div>
                        </div>
                       
                        {{-- <div class="row">
                            <form id="importform" method="POST" action="{{ route('pincode.bulk.upload')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Select Pincode Import File</label>
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-4 mt-3 pt-5">
                                        <button type="submit"  class="btn btn-primary mb-2">Import</button>
                                    </div>
                                </div>
                            </form>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <h3> Stock Quantity Update </h3>
                    <div class="row">
                        <div class="col-8">
                            <form id="stockform" method="POST" action="{{ route('products.stock.upload') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Select Import File</label>
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-4 mt-3 pt-5">
                                        <button type="submit" class="btn btn-primary mb-2">Import</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-4">
                            <label for=""> Sample Stock Excel file </label>
                            <div class="mt-2">
                                <a href="{{ asset('assets/data/product_stock_update.xlsx') }}"> <i
                                        class="mdi mdi-file h2"></i> Download Sample</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <h3> Product Filter and Specification Attributes Upload</h3>
                    <div class="row">
                        <div class="col-8">
                            <form id="attributeform" method="POST" action="{{ route('products.attribute.upload') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Select Import File</label>
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-4 mt-3 pt-5">
                                        <button type="submit" class="btn btn-primary mb-2">Import</button>                                   
                                        <a href="{{route('product_attriut_set_export')}}"><button type="button" class="btn btn-success  mb-2 pl-3">Export</button></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-4">
                            <label for=""> Sample Attributes Excel file </label>
                            <div class="mt-2">
                                <a href="{{ asset('assets/data/product_attributes.xlsx') }}"> <i
                                        class="mdi mdi-file h2"></i> Download Sample</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <h3>Related Product</h3>
                    <div class="row">
                        <div class="col-8">
                            <form id="importrelatedform" method="POST" action="{{ route('related.products.bulk.upload') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Select Import File</label>
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-4 mt-3 pt-5">
                                        <button type="submit" class="btn btn-primary mb-2">Import</button>                                   
                                        <a href="{{route('related_product_set_export')}}"><button type="button" class="btn btn-success  mb-2 pl-3">Export</button></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-4">
                            <label for=""> Sample Attributes Excel file </label>
                            <div class="mt-2">
                                <a href="{{ asset('assets/data/related_product_arrttiute.xlsx') }}"> <i
                                        class="mdi mdi-file h2"></i> Download Sample</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="card">
<div class="card-body">
<div class="row">
    <h3>Product Image Upload</h3>
    <div class="row">
        <div class="col-8">
            <form id="importrelatedimage" method="POST" action="{{ route('upload.bulk.image') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-4 mt-3 pt-5">
    <button type="submit"  id="imagesync" class="button btn-primary" onclick="this.classList.toggle('button--loading')">
    <span class="button__text">Image Sync</span>
</button>                             
                      
                    </div>
                </div>
            </form>
        </div>
        
    </div>
</div>
<br>
<div class="row">
    <h3>Gallery Image Upload</h3>
    <div class="row">
        <div class="col-8">
            <form id="importrelatedimage1" method="POST" action="{{ route('upload.bulk.gallery_image') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-4 mt-3 pt-5">
    <button type="submit"  id="imagesync1" class="button btn-primary" onclick="this.classList.toggle('button--loading')">
    <span class="button__text">Gallery Sync</span>
</button>                             
                      
                    </div>
                </div>
            </form>
        </div>
        
    </div>
</div>
</div>
</div>
    </div>
@endsection
@section('add_on_script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="{{ asset('assets/js/datatable.min.js') }}"></script>
@if($message = Session::get('success'))
<script type="text/javascript">
toastr.success('Success',"{{ $message }}");
</script>
@endif
    <script>
        $(document).ready(function() {
    $("#imagesync").click(function(event) {
       this.disabled = true;
    document.getElementById('importrelatedimage').submit();
   });
    $("#imagesync1").click(function(event) {
       this.disabled = true;
    document.getElementById('importrelatedimage1').submit();
   });
            $("#importform").validate({
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        contentType: false,
                        processData: false,

                        success: function(response) {

                            if (response.error == 0) {
                                toastr.success('Success', response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                toastr.error('Error', response.message);
                            }

                        }
                    });

                }
            });
            $("#importrelatedform").validate({
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        contentType: false,
                        processData: false,

                        success: function(response) {

                            if (response.error == 0) {
                                toastr.success('Success', response.message);
                                $('#importrelatedform')[0].reset();
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                toastr.error('Error', response.message);
                            }

                        }
                    });

                }
            });

            $("#stockform").validate({
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        contentType: false,
                        processData: false,

                        success: function(response) {

                            if (response.error == 0) {
                                toastr.success('Success', response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                toastr.error('Error', response.message);
                            }

                        }
                    });

                }
            });

            $("#attributeform").validate({
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        contentType: false,
                        processData: false,

                        success: function(response) {

                            if (response.error == 0) {
                                toastr.success('Success', response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                toastr.error('Error', response.message);
                            }

                        }
                    });

                }
            });
        }) 

        function exportAttriuteSet()
        {
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        $.ajax({
            url: '{{ route("product_attriut_set_export")}}',
            type: 'GET',
            cache: false,
        xhrFields:{
            responseType: 'blob'
        },
           // data: {id:productImageId},
           
                success: function(result, status, xhr) {

var disposition = xhr.getResponseHeader('content-disposition');
var matches = /"([^"]*)"/.exec(disposition);
var filename = (matches != null && matches[1] ? matches[1] : 'salary.xlsx');

// The actual download
var blob = new Blob([result], {
    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
});
var link = document.createElement('a');
link.href = window.URL.createObjectURL(blob);
link.download = filename;

document.body.appendChild(link);

link.click();
document.body.removeChild(link);
}
            
        });

    }
        
    </script>
@endsection
