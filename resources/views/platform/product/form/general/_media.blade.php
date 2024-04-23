<div class="card-header">
    <div class="card-title">
        <h2>Media</h2>
    </div>
</div>
<style>
    #preview-parent {
        display: flex;
        flex-wrap: wrap;
    }

    .preview {
        display: flex;
        flex-direction: column;
        margin: 1rem;
    }

    img{
        width: 100%;
        height: 100px;
        object-fit: cover;
    }
    .logo{
        height:55px !important;
    }
</style>
<div class="card-body pt-0">
    <div class="fv-row mb-2">
        <div class="dropzone" id="">
            <div>
                <input type="file" name="gallery[]" id="gallery_image" multiple>
            </div>

            <div id="preview-parent">
                @if (isset($images) && !empty($images))
                    @foreach ($images as $item)
                        <div class="preview">
                            <img class="gallery_img" src="{{ $item['url'] }}">
                            <div class="d-flex ">
                                <div class="">
                                    <input type="number" class="w-50px h-35px" min="1" name="image_order"
                                        value="{{ $item['order_by'] }}" onkeyup="changeOrder(this.value, {{ $item['id'] }})">
                                </div>
                                <div class="">
                                    <button type="button" class="btn btn-danger btn-sm small"
                                        data-index="{{ $item['id'] }}"
                                        onclick="deleteImage(this, {{ $item['id'] }})">
                                        Delete

                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
    <div class="text-muted fs-7">Set the product media gallery.</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    function changeOrder(order_no, id) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('products.image.order') }}",
            type: 'POST',
            data: {
              id: id,
              order_no: order_no
            },
            success: function(res) {

              if(res.error == 1 ){
                toastr.success('success', res.message );
              }

            }
        });
    }
    var dataTransfer = new DataTransfer()

    const input = document.querySelector('#gallery_image')

    input.addEventListener('change', () => {

        let files = input.files

        for (let i = 0; i < files.length; i++) {
            // A new upload must not replace images but be added
            dataTransfer.items.add(files[i])

            // Generate previews using FileReader
            let reader, preview, previewImage
            reader = new FileReader()

            preview = document.createElement('div')
            previewImage = document.createElement('img')
            deleteButton = document.createElement('button')
            orderInput = document.createElement('input')

            preview.classList.add('preview')
            deleteButton.classList.add('btn')
            deleteButton.classList.add('btn-sm')
            deleteButton.classList.add('btn-light-danger')
            document.querySelector('#preview-parent').appendChild(preview)
            deleteButton.setAttribute('data-index', i)
            deleteButton.setAttribute('onclick', 'deleteImage(this)')
            deleteButton.innerText = 'Delete'
            orderInput.type = 'hidden'
            orderInput.name = 'images_order[' + files[i].name + ']'

            preview.appendChild(previewImage)
            preview.appendChild(deleteButton)
            preview.appendChild(orderInput)

            reader.readAsDataURL(files[i])
            reader.onloadend = () => {
                previewImage.src = reader.result
            }
        }

        // Update order values for all images
        updateOrder()
        // Finally update input files that will be sumbitted
        input.files = dataTransfer.files
    })

    const updateOrder = () => {
        let orderInputs = document.querySelectorAll('input[name^="images_order"]')
        let deleteButtons = document.querySelectorAll('button[data-index]')
        for (let i = 0; i < orderInputs.length; i++) {
            orderInputs[i].value = [i]
            deleteButtons[i].dataset.index = [i]

            // Just to show that order is always correct I add index here
            deleteButtons[i].innerText = 'Delete'
        }
    }

    const deleteImage = (item, id = '') => {
        console.log(item, 'delete item');
        if (id) {
            deleteGalleryImageFromPane(id, item);
        } else {

            dataTransfer.items.remove(item.dataset.index)
            input.files = dataTransfer.files
            // Delete element from DOM and update order
            item.parentNode.remove()
            updateOrder()
        }
        // Remove image from DataTransfer and update input
    }

    // I make the images sortable by means of SortableJS
    const el = document.getElementById('preview-parent')
    new Sortable(el, {
        animation: 150,

        // Update order values every time a change is made
        onEnd: (event) => {
            updateOrder()
        }
    })

    function deleteGalleryImageFromPane(id, item) {

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('products.remove.image') }}",
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(res) {

                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )

                        $(item).parent().parent().parent().remove()

                    }
                });

            }
        })
    }
</script>
