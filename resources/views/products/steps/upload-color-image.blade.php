<div class="modal fade" id="chooseColorImageModal" tabindex="-1" aria-labelledby="chooseColorImageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex gap-2" id="chooseColorImageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input type="file" accept="image/*" id="choose_color_image" name="choose_color_image" label="Choose Image" data-selected-id="{{ $color['id'] }}" required />
                        </div>
                        <div class="col-md-6 mt-4">
                            <button type="button" class="btn btn-google w-100 search-image-modal">
                                <img src="https://www.google.com/favicon.ico" alt="Google Logo" class="google-logo">
                                Choose from Google
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <img id="previewImage" src="" alt="Image Preview" style="max-width: 200px; display: none;">
                    </div>
                </div>
                {{--<button type="button" class="btn btn-primary" id="uploadImageButton" disabled>Upload</button>--}}
                <button type="button" class="btn btn-danger" id="deleteImageButton" disabled>Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="googleImagesModal" tabindex="-1" aria-labelledby="googleImagesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="googleImagesModalLabel">Select Image For Color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
            $(function() {
                document.getElementById('choose_color_image').addEventListener('change', function(event) {
                let file = event.target.files[0]; 
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        let img = document.getElementById('previewImage');
                        img.src = e.target.result; 
                        img.style.display = 'block'; 
                    };
                    reader.readAsDataURL(file); 
                }

                let fileInput = document.getElementById('choose_color_image');

                let inputName = fileInput.getAttribute('data-selected-id');
                console.log('Selected ID:', inputName); 

                let targetInput = document.querySelector(`[name="image[${inputName}]"]`);
                console.log('Target Input:', targetInput); 

                if (targetInput) {
                    let dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file); 

                    targetInput.files = dataTransfer.files;

                    let changeEvent = new Event('change', { bubbles: true });
                    targetInput.dispatchEvent(changeEvent);
                } else {
                    console.error('Target input not found!');
                }
            });


            $('#choose_color_image').change(function() {
                if ($(this)[0].files.length === 0) {
                    $('#uploadImageButton').attr('disabled', 'disabled');
                } else {
                    $('#uploadImageButton').removeAttr('disabled');
                }
            });

            function fetchImageBinary(imageUrl, selectedColorId) {
                fetch(imageUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        const fileName = "image.jpg"; 
                        const file = new File([blob], fileName, { type: blob.type });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);

                        let inputElement = document.querySelector(`[name="image[${selectedColorId}]"]`);
                        if (inputElement) {
                            inputElement.files = dataTransfer.files;

                            let formData = new FormData();
                            formData.append(`image[${selectedColorId}]`, file);
                            
                            console.log("File set in input:", inputElement.files[0]);
                        } else {
                            console.error('File input not found for:', selectedColorId);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching image:', error);
                    });
            }



            let selectedColor = null;
            let article = null;

            @if (isset($product))
                article = @json($product->load('brand', 'productType'));
            @else
                article = @json($savingProduct);
            @endif

            $(document).on('click', '.change-color-image-modal', function() {
                document.getElementById('choose_color_image').value = null;
                let parentElement = $(this).parents('[data-color-detail]');
                selectedColor = parentElement.data('color-detail');
                $('#choose_color_image').attr('data-selected-id',selectedColor.id);
                let colorBox =
                    `<div class="d-inline-block px-3" style="background: ${selectedColor.ui_color_code};"></div>`;
                $('#chooseColorImageModal .modal-title').html(
                    `${colorBox} ${selectedColor.name || selectedColor.color_name} (${selectedColor.code || selectedColor.color_code})`
                );

                $('#chooseColorImageModal').modal('show');

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('color_id', selectedColor.id);
                formData.append('article_id', article.id);

                $.ajax({
                    url: '{{ route("products.colors.fetch-image") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.success){
                            $('#previewImage').attr('src',response.imagePath).css('display','block');
                            $('#deleteImageButton').removeAttr('disabled');
                        }else{
                            $('#previewImage').attr('src','').hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Image error:', error);
                    }
                });
            });

            $(document).on('click', '#deleteImageButton', function() {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('color_id', selectedColor.id);
                formData.append('article_id', article.id);
                swal({
                    title: "Are you sure?",
                    text: `You really want to remove this Image?`,
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: '{{ route("products.colors.delete-image") }}',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if(response.success){
                                    $('#previewImage').attr('src','').css('display','none');
                                    $('#deleteImageButton').attr('disabled', 'disabled');
                                    Swal.fire("Deleted!", "Image has been deleted.", "success");
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });

            // Open the Google Images modal
            $('.search-image-modal').click(function() {
                $('#googleImagesModal').modal('show');
                initialize();
            });

            // Google Custom Search API variables
            const searchEngineId = '{{ setting("google_search_engine_id") }}';
            const googleSearchApiKey = '{{ setting("google_search_api_key") }}';
            const resultsPerPage = 10;
            let startIndex = 1;
            let allItems = [];

            // Function to fetch images from Google Custom Search API
            function fetchImages(startIndex, searchKeyword) {
                return $.ajax({
                    url: 'https://www.googleapis.com/customsearch/v1',
                    method: 'GET',
                    data: {
                        q: searchKeyword,
                        cx: searchEngineId,
                        searchType: 'image',
                        key: googleSearchApiKey,
                        start: startIndex
                    }
                });
            }

            function renderImages(items) {
                let newContent = '';
                $(items).each(function() {
                    newContent += `
                        <div class="col-md-3">
                            <img src="${this.link}" alt="${this.title}" data-image-url="${this.link}" class="img-fluid" />
                        </div>
                    `;
                });
                $('#googleImagesModal .image-container').append(newContent);
            }

            function loadMoreImages(searchKeyword) {
                fetchImages(startIndex, searchKeyword).done(function(response) {
                    if (response.items && response.items.length > 0) {
                        allItems = allItems.concat(response.items);
                        renderImages(response.items);
                        startIndex += resultsPerPage;

                        if (response.queries.nextPage) {
                            $('#loadMoreButton').show();
                        } else {
                            $('#loadMoreButton').hide();
                        }
                    } else {
                        $('#loadMoreButton').hide();
                    }
                }).fail(function(apiErr) {
                    let {
                        errors
                    } = apiErr.responseJSON.error;

                    errors.forEach(err => {
                        const alertHTML = `
                            <div class="alert alert-danger fade show mt-2" role="alert">
                                <strong>Error!</strong> ${err.message}
                            </div>
                        `;
                        $('#googleImagesModal .modal-body').append(alertHTML);
                    });


                    $('#loadMoreButton').hide();
                });
            }


            function initialize() {
                const searchImageContent = `
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8 d-flex gap-2">
                            <input type="text" id="google_search_keyword" class="form-control" placeholder="Enter search keyword">
                            <button type="button" class="btn btn-primary search-by-keyword">Search</button>
                        </div>
                    </div>
                    <div class="row image-container"></div>
                    <div class="text-center my-1">
                        <button id="loadMoreButton" type="button" class="btn btn-primary">Load More</button>
                    </div>
                `;
                $('#googleImagesModal .modal-body').html(searchImageContent);

                // Set default search query
                const searchQuery =
                    `${article.brand?.name || ''} ${article.manufacture_code} ${article.short_description} ${article.product_type?.name || ''}`;
                $('#google_search_keyword').val(searchQuery);

                // Search button click event
                $('.search-by-keyword').click(function() {
                    const searchKeyword = $('#google_search_keyword').val();
                    startIndex = 1;
                    allItems = [];
                    $('#googleImagesModal .image-container').html('');
                    loadMoreImages(searchKeyword);
                });

                // Load more button click event
                $('#loadMoreButton').click(function() {
                    const searchKeyword = $('#google_search_keyword').val();
                    loadMoreImages(searchKeyword);
                });

                // Initial load with default search query
                loadMoreImages(searchQuery);
            }

            // Handle image selection in the Google Images modal
            $('#googleImagesModal').on('click', 'img', function() {
                const imageUrl = $(this).data('image-url');
                fetchImageBinary(imageUrl, selectedColor.id)
                $('#googleImagesModal img').removeClass('selected-image');
                $(this).addClass('selected-image');
            });

           /*  $('#uploadImageButton').click(function() {
                const fileInput = $('#choose_color_image')[0];
                const file = fileInput.files[0];

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('image', file);
                formData.append('color_id', selectedColor.id);
                formData.append('article_id', article.id);

                $.ajax({
                    url: '{{ route("products.colors.upload-image") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Upload response:', response);
                        $('#chooseColorImageModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', error);
                    }
                });
            });
 */
        });
    </script>
@endpush
