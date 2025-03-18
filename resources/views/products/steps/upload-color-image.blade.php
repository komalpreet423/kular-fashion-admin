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
                            <x-form-input type="file" name="choose_color_image" label="Choose Image" required />
                        </div>
                        <div class="col-md-6 mt-4">
                            <button type="button" class="btn btn-google w-100 search-image-modal">
                                <img src="https://www.google.com/favicon.ico" alt="Google Logo" class="google-logo">
                                Choose from Google
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary">Upload</button>
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
            // Function to fetch image binary data
            function fetchImageBinary(imageUrl) {
                fetch(imageUrl, {
                        mode: 'no-cors'
                    })
                    .then(response => response.blob())
                    .then(blob => {
                        const reader = new FileReader();

                        reader.onloadend = function() {
                            const file = new File([blob], "image.jpg", {
                                type: blob.type
                            });

                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);

                            $('#choose_color_image')[0].files = dataTransfer.files;
                        };

                        reader.readAsDataURL(blob);
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

            // Open the color image modal
            $(document).on('click', '.change-color-image-modal', function() {
                let parentElement = $(this).parents('[data-color-detail]');
                selectedColor = parentElement.data('color-detail');

                let colorBox =
                    `<div class="d-inline-block px-3" style="background: ${selectedColor.ui_color_code};"></div>`;
                $('#chooseColorImageModal .modal-title').html(
                    `${colorBox} ${selectedColor.name || selectedColor.color_name} (${selectedColor.code || selectedColor.color_code})`
                );

                $('#chooseColorImageModal').modal('show');
            });

            // Open the Google Images modal
            $('.search-image-modal').click(function() {
                $('#googleImagesModal').modal('show');
                initialize();
            });

            // Google Custom Search API variables
            const googleSearchApiKey = 'AIzaSyCqfteqFcsn7rIbUXKEJdBxqdD8_2B6rSA';
            const searchEngineId = '940f102d41cdc446e';
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
                $('#googleImagesModal .image-container').append(newContent); // Use append instead of html
            }

            function loadMoreImages(searchKeyword) {
                fetchImages(startIndex, searchKeyword).done(function(response) {
                    if (response.items && response.items.length > 0) {
                        allItems = allItems.concat(response.items);
                        renderImages(response.items); // This will now append the new images
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
                            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                                <strong>Error!</strong> ${err.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    startIndex = 1; // Reset start index for new search
                    allItems = []; // Clear previous items
                    $('#googleImagesModal .image-container').html(
                        ''); // Clear the container before new search
                    loadMoreImages(searchKeyword); // Fetch new images
                });

                // Load more button click event
                $('#loadMoreButton').click(function() {
                    const searchKeyword = $('#google_search_keyword').val();
                    loadMoreImages(searchKeyword); // Fetch more images
                });

                // Initial load with default search query
                loadMoreImages(searchQuery);
            }

            // Handle image selection in the Google Images modal
            $('#googleImagesModal').on('click', 'img', function() {
                const imageUrl = $(this).data('image-url');
                $('#googleImagesModal img').removeClass('selected-image');
                $(this).addClass('selected-image');
                fetchImageBinary(imageUrl);
            });
        });
    </script>
@endpush
