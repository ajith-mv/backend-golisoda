<script>
    
    const element = document.getElementById('kt_modal_export');
    const exportModal = new bootstrap.Modal(element);

    function openExportForm(export_type) {
        
        $('#export_type').val( export_type );
        var title = (export_type.replace("_", " ")).toUpperCase();
        title = (export_type.replace("-", " ")).toUpperCase();
        $('#export_modal_title').html( 'EXPORT '+ title );
        exportModal.show();
    }

    $('.export_modal_close').click(function(){
        exportModal.hide();
    })

    function openForm(module_type, id = '', from = '', dynamicModel = '', categoryId = '' ) {
        
        // if( from == 'product' ) {
        //     categoryId = $('#category_id').val();
        //     if( categoryId == '' || categoryId == null || categoryId == undefined ) {
        //         Swal.fire({
        //             html: 'Please select Product category',
        //             icon: "error",
        //             buttonsStyling: false,
        //             confirmButtonText: "Ok, got it!",
        //             customClass: {
        //                 confirmButton: "btn btn-primary"
        //             }
        //             });
        //         return false;
        //     }
        // }
        
       
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: config.routes[module_type].add,
            type: 'POST',
            data: {id:id, from:from, dynamicModel:dynamicModel, categoryId:categoryId},
            success: function(res) {
                $( '#form-common-content' ).html(res);
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.show();
                return false;
            }, error: function(xhr,err){
                if( xhr.status == 403 ) {
                    toastr.error(xhr.statusText, 'UnAuthorized Access');
                }
            }
        });

    }

    function exportExcel(module_type) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            url: config.routes[module_type].export.excel,
            type: 'POST',
            success: function(result, status, xhr) {

                var disposition = xhr.getResponseHeader('content-disposition');
                var matches = /"([^"]*)"/.exec(disposition);
                var filename = (matches != null && matches[1] ? matches[1] : module_type+'.xlsx');

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
                
            },
            error: function(xhr,err){
                if( xhr.status == 403 ) {
                    toastr.error(xhr.statusText, 'UnAuthorized Access');
                }
            }
        });

    }

    function commonDelete(id, module_type) {
        Swal.fire({
            text: "Are you sure you would like to delete?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, return",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function (result) {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: config.routes[module_type].delete,
                    type: 'POST',
                    data: {id:id},
                    success: function(res) {
                        dtTable.ajax.reload();
                        Swal.fire({
                            title: "Deleted!",
                            text: res.message,
                            icon: "success",
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-success"
                            },
                            timer: 3000
                        });
                        
                    },
                    error: function(xhr,err){
                        if( xhr.status == 403 ) {
                            toastr.error(xhr.statusText, 'UnAuthorized Access');
                        }
                    }
                });		
            }
        });
    }

    function commonChangeStatus(id, status, module_type) {
        Swal.fire({
            text: "Are you sure you would like to change status?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, Change it!",
            cancelButtonText: "No, return",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function (result) {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
               
                $.ajax({
                    url: config.routes[module_type].status,
                    type: 'POST',
                    data: {id:id, status:status},
                    success: function(res) {
                        dtTable.ajax.reload();
                        Swal.fire({
                            title: "Updated!",
                            text: res.message,
                            icon: "success",
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-success"
                            },
                            timer: 3000
                        });
                        
                    },
                    error: function(xhr,err){
                        if( xhr.status == 403 ) {
                            toastr.error(xhr.statusText, 'UnAuthorized Access');
                        }
                    }
                });		
            }
        });
    }
    $(document).ready(function () {    
        $('.numberonly').keypress(function (e) {    
            var charCode = (e.which) ? e.which : event.keyCode    
            if (String.fromCharCode(charCode).match(/[^0-9]/g))    
                return false;                        
        });    

    }); 

    $('.mobile_num').keypress(
        function(event) {
            if (event.keyCode == 46 || event.keyCode == 8) {
                //do nothing
            } else {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            }
        }
    );
    

    function getProductCategoryDropdown(id = '' ) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("common.category.dropdown") }}',
            type: 'POST',
            data: {id:id},
            success: function(res) {
                $( '#product-category' ).html(res);
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.hide();
                return false;
            }
            
        });

    }

    function getProductBrandDropdown(id = '' ) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("common.brand.dropdown") }}',
            type: 'POST',
            data: {id:id},
            success: function(res) {
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.hide();
                
                $( '#product-category-brand' ).html(res);
            
                return false;
            }
            
        });

    }

    function getProductWarrantyDropdown(id = '' ) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("common.warranty.dropdown") }}',
            type: 'POST',
            data: {id:id},
            success: function(res) {
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.hide();
                
                $( '#product-warranty-pane' ).html(res);
            
                return false;
            }
            
        });

    }

    function getProductDynamicDropdown( id = '', tag = '' ) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route("common.dynamic.dropdown") }}',
            type: 'POST',
            data: {id:id, tag:tag},
            success: function(res) {
                $( '#'+tag ).html(res);
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.hide();
                return false;
            }
            
        });

    }
    $('#search-form').on('submit', function(e) {
        var filter_val =  $('#filter_status').val();
            if(filter_val == "published" || "unpublished")
            {
                $('#btn-light-primary').removeClass('btn-light-primary');
                $('#btn-light-primary').addClass('btn-light-danger');
            }
            if(filter_val == "0" || null)
            {
                $('#btn-light-primary').addClass('btn-light-primary');
                $('#btn-light-primary').removeClass('btn-light-danger');
            }

        });
        $('#search-form').on('reset', function(e) {
            $('#btn-light-primary').addClass('btn-light-primary');
            $('#btn-light-primary').removeClass('btn-light-danger');
        });
        $('.numberonly').keypress(function (e) {    
        var charCode = (e.which) ? e.which : event.keyCode    
        if (String.fromCharCode(charCode).match(/[^0-9]/g))    
            return false;                        
    }); 
</script>