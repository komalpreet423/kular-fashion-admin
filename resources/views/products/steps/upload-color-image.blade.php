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

                            $('#choose_color_image')[0].files = dataTransfer.files
                        };

                        reader.readAsDataURL(blob);
                    })
                    .catch(error => {
                        console.error('Error fetching image:', error);
                    });
            }

            let selectedColor = null;
            let article = null;

            @if(isset($product))
                article = @json($product->load('brand', 'productType'));
            @else
                article = @json($savingProduct);
            @endif

            $(document).on('click', '.change-color-image-modal', function() {
                let parentElement = $(this).parents('[data-color-detail]');
                selectedColor = parentElement.data('color-detail');

                let colorBox =
                    `<div class="d-inline-block px-3" style="background: ${selectedColor.ui_color_code};"></div>`
                $('#chooseColorImageModal .modal-title').html(`${colorBox} ${selectedColor.name || selectedColor.color_name} (${selectedColor.code || selectedColor.color_code})`);

                $('#chooseColorImageModal').modal('show');
            });

            $('.search-image-modal').click(function() {
                $('#googleImagesModal').modal('show');
                let searchImageContent = ``;
                const googleSearchApiKey = 'AIzaSyCqfteqFcsn7rIbUXKEJdBxqdD8_2B6rSA';
                const searchEngineId = '940f102d41cdc446e';
                const searchQuery = `${article.brand?.name || ''} ${article.manufacture_code} ${article.short_description} ${article.product_type?.name || ''}`;
                const resultsPerPage = 10;
                let startIndex = 1;
                let allItems = [];

                function fetchImages(startIndex) {
                    return $.ajax({
                        url: 'https://www.googleapis.com/customsearch/v1',
                        method: 'GET',
                        data: {
                            q: searchQuery,
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

                function loadMoreImages() {
                    fetchImages(startIndex).done(function(response) {
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
                    }).fail(function(error) {
                        console.error('Error fetching images:', error);
                        $('#loadMoreButton').hide();
                    });
                }

                function initialize() {
                    searchImageContent = `<div class="row image-container"></div>`;
                    searchImageContent += `<div class="text-center my-1">
                        <button id="loadMoreButton" type="button" class="btn btn-primary">Load More</button>
                    </div>`;
                    $('#googleImagesModal .modal-body').html(searchImageContent);

                    loadMoreImages();

                    $('#loadMoreButton').click(function() {
                        loadMoreImages();
                    });
                }

                initialize();
            });

            $('#googleImagesModal').on('click', 'img', function() {
                const imageUrl = $(this).data('image-url');
                $('#googleImagesModal img').removeClass('selected-image');
                $(this).addClass('selected-image');
                fetchImageBinary(imageUrl);
            });
        })
    </script>
@endpush
