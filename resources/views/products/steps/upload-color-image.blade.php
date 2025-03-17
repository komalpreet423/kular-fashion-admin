@foreach ($savedColors as $color)
<div class="modal fade" id="colorModal{{ $color['id'] }}" tabindex="-1" aria-labelledby="colorModalLabel{{ $color['id'] }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="colorModalLabel{{ $color['id'] }}">{{ $color['name'] }} ({{ $color['code'] }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Color Code:</strong> {{ $color['code'] }}</p>
                <p>
                    <strong>UI Color:</strong>
                    <span class="d-inline-block p-2" style="background: {{ $color['ui_color_code'] }}; width: 50px; height: 50px;"></span>
                </p>

                <!-- Display Uploaded Image -->
                <div class="mb-3">
                    <label class="form-label"><strong>Uploaded Image:</strong></label>
                    <div>
                        @if(isset($color['image']))
                        <img src="{{ asset('uploads/colors/' . $color['image']) }}" class="img-fluid rounded" alt="Color Image" width="150">
                        @else
                        <p class="text-muted">No image uploaded</p>
                        @endif
                    </div>
                </div>

                <!-- Image Upload Form -->
                @csrf
                <div class="mb-3">
                    <label for="colorImage{{ $color['id'] }}" class="form-label"><strong>Upload Color Image:</strong></label>
                    <div class="row m-1">
                        <div class="col-md-7">
                            <input type="file" class="form-control" name="color_image" id="colorImage{{ $color['id'] }}" required>
                        </div>
                        <div class="col-md-5">
                            <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#googleSelectModal{{ $color['id'] }}">
                                Select from Google
                            </button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Google Image Selection Modal (Unique per Color) -->
<div class="modal fade" id="googleSelectModal{{ $color['id'] }}" tabindex="-1" aria-labelledby="googleSelectModalLabel{{ $color['id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="googleSelectModalLabel{{ $color['id'] }}">Select Image from Google</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Display a gallery of images fetched from Google or integrate an image search API here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary select-google-image-btn" data-color-id="{{ $color['id'] }}">Select Image</button>
            </div>
        </div>
    </div>
</div>
@endforeach
