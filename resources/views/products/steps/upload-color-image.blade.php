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
                            <x-form-input type="file" accept="image/*" id="choose_color_image"
                                name="choose_color_image" label="Choose Image" required />
                        </div>
                        <div class="col-md-6 mt-4">
                            <button type="button" class="btn btn-google w-100 search-image-modal">
                                <img src="https://www.google.com/favicon.ico" alt="Google Logo" class="google-logo">
                                Choose from Google
                            </button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <img src="" id="image-preview-popup" style="display:none" width="30 0" height="300">
                    </div>
                </div>
                <button type="button" class="btn btn-danger d-none" id="deleteImageButton">
                    <i class="fa fa-trash me-2"></i>Remove
                </button>
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

<input type="hidden" name="color_images_to_be_deleted">

@push('scripts')
    <script>
        $(function() {
            let defaultImage = '{{ asset("assets/images/default.webp") }}';

            function fetchBinaryImage(imageUrl, selectedColorId) {
                fetch(imageUrl, { mode: 'no-cors' })
                    .then(response => {
                        if (!response.ok) {
                            $(`#preview-color-image-${selectedColorId}`).addClass('border border-danger border-3');
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        const mimeType = blob.type;
                        const fileExtension = mimeType.split('/')[1]; 
                        const fileName = `image.${fileExtension}`;
                        const file = new File([blob], fileName, {
                            type: fileExtension
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);

                        $(`[name="image[${selectedColorId}]"]`)[0].files = dataTransfer.files;
                        $(`#preview-color-image-${selectedColorId}`).removeClass('border border-danger border-3');

                        let formData = new FormData();
                        formData.append(`image[${selectedColorId}]`, file);
                    })
                    .catch(error => {
                        $(`#preview-color-image-${selectedColorId}`).addClass('border border-danger border-3');
                        console.error('Error fetching image:', error);
                    });
            }

            let selectedColor = null;
            let article = null;

            $('#choose_color_image').on('change', function(event) {
                const file = event.target.files[0];
                if (!file) return;

                previewImage(event, `color-image-${selectedColor.id}`);

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                $(`[name="image[${selectedColor.id}]"]`)[0].files = dataTransfer.files;

                let colorIdsInput = $('[name="color_images_to_be_deleted"]');
                let currentValues = colorIdsInput.val().split(',').filter(Boolean);
                currentValues = currentValues.filter(value => value !== selectedColor.id.toString());
                colorIdsInput.val(currentValues.join(','));

                $(this).val('');
                $(`[data-id="rm-${selectedColor.id}"]`).attr('data-color-image', 'is-changed-image');
                $('#chooseColorImageModal').modal('hide');
            });

            @if (isset($product))
                article = @json($product->load('brand', 'productType'));
            @else
                article = @json($savingProduct);
            @endif

            $(document).on('click', '.change-color-image-modal', function() {
                let parentElement = $(this).closest('[data-color-detail]');
                selectedColor = parentElement.data('color-detail');

                selectedImage = $(this).find('img').attr('src');
                $('#image-preview-popup').attr('src',selectedImage).show();

                if(!selectedColor.id && selectedColor.color_id){
                    selectedColor.id = selectedColor.color_id;
                }

                //console.log('selectedColor', selectedColor)
                let colorBox =
                    `<div class="d-inline-block px-3" style="background: ${selectedColor.ui_color_code};"></div>`;

                $('#chooseColorImageModal .modal-title').html(
                    `${colorBox} ${selectedColor.name || selectedColor.color_name} (${selectedColor.code || selectedColor.color_code})`
                );

                $('#chooseColorImageModal').modal('show');

                let imagePath = parentElement.attr('data-color-image');
                if (imagePath) {
                    $('#deleteImageButton').removeClass('d-none');
                } else {
                    $('#deleteImageButton').addClass('d-none');
                }
            });

            $(document).on('click', '#deleteImageButton', function() {
                swal({
                    title: "Are you sure?",
                    text: `You really want to remove this Image?`,
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                }, function(isConfirm) {
                    if (isConfirm) {
                        let colorIdsInput = $('[name="color_images_to_be_deleted"]');

                        let currentValues = colorIdsInput.val().split(',').filter(Boolean);
                        if (!currentValues.includes(selectedColor.id.toString())) {
                            currentValues.push(selectedColor.id);
                            colorIdsInput.val(currentValues.join(','));
                        }

                        swal.close();
                        $('#chooseColorImageModal').modal('hide');

                        $(`#preview-color-image-${selectedColor.id}`).attr('src', defaultImage);
                        $(`[data-id="rm-${selectedColor.id}"]`).removeAttr('data-color-image');
                    }
                });
            });

            // Open the Google Images modal
            $('.search-image-modal').click(function() {
                $('#googleImagesModal').modal('show');
                initialize();
            });

            // Google Custom Search API variables
            const searchEngineId = '{{ setting('google_search_engine_id') }}';
            const googleSearchApiKey = '{{ setting('google_search_api_key') }}';
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
                        $('#googleImagesModal .modal-body .alert').remove();

                        allItems = allItems.concat(response.items);
                        renderImages(response.items);
                        startIndex += resultsPerPage;

                        if (response.queries.nextPage) {
                            $('#loadMoreButton').show();
                        } else {
                            $('#loadMoreButton').hide();
                        }
                    } else {
                        const alertHTML = `
                            <div class="alert alert-danger fade show mt-2" role="alert" id="alert-msg">
                                <strong>Oops!</strong> Search results not found.
                            </div>
                        `;
                        var msgCount = $('div#alert-msg').length;
                        if(msgCount == 0){
                            $('#googleImagesModal .modal-body').append(alertHTML);
                        }
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
                    <div class="row image-container my-2"></div>
                    <div class="text-center my-1">
                        <button id="loadMoreButton" type="button" class="btn btn-primary">Load More</button>
                    </div>
                `;
                $('#googleImagesModal .modal-body').html(searchImageContent);

                // Set default search query
                const searchQuery =
                    `${article.brand?.name || ''} ${article.manufacture_code} ${article.short_description} ${article.product_type?.name || ''}`;
                $('#google_search_keyword').val(searchQuery);

                $('#google_search_keyword').keydown(function(event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        $('.search-by-keyword').click(); 
                        return false;
                    }
                });

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
                fetchBinaryImage(imageUrl, selectedColor.id);
                
                $(`#preview-color-image-${selectedColor.id}`).attr('src', imageUrl);
                $('#googleImagesModal img').removeClass('selected-image');
                $(this).addClass('selected-image');
                $('#googleImagesModal').modal('hide');
                $('#chooseColorImageModal').modal('hide');

                $(`[data-id="rm-${selectedColor.id}"]`).attr('data-color-image', imageUrl);
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.color_qty').on('keyup', function () {
                const main_id = $(this).attr('id').split('_')[2];
                let total = 0;

                $('input[id^="color_qty_'+main_id+'_"]').each(function () {
                    const value = parseFloat($(this).val());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });

                $('#color_qty_sum_' + main_id).text(total);
            });
        });
    </script>
@endpush
