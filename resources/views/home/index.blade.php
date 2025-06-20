@extends('layouts.app')
@section('title', 'Home')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <x-error-message :message="$errors->first('message')" />
                <x-success-message :message="session('success')" />

                <div class="card shadow rounded">
                    <div class="card-body">
                        <div class="row text-center mb-1">
                            <div class="col-12">
                                <h2>Slider Images</h2>
                            </div>
                        </div>

                        <div class="row text-center mb-2">
                            @foreach ($images->where('type', 'slider') as $img)
                                <div class="col-md-4 ">
                                    <div class="card image-wrapper position-relative">
                                        <button data-endpoint="{{ route('home.destroy', $img->id) }}"
                                            data-source="image"
                                            class="delete-btn btn btn-danger btn-sm py-0 px-1 position-absolute top-0 end-0 m-2"
                                            title="Delete" style="z-index: 10;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <img src="{{ asset($img->image_path) }}"
                                            class="card-img-top fixed-image draggable-image" alt="Slider Image"
                                            draggable="true">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row justify-content-center mb-2">
                            <div class="col-md-6 text-center">
                                <form action="{{ route('images.upload') }}" method="POST" enctype="multipart/form-data"
                                    id="sliderForm">
                                    @csrf
                                    <input type="file" name="slider_images[]" id="sliderInput" accept="image/*"
                                        multiple style="display: none">
                                    <button type="button" class="btn btn-primary btn-sm" id="uploadSliderBtn">
                                        Upload Slider Images
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="row text-center mb-2">
                            <div class="col-12">
                                <h2>Newsletter Image</h2>
                            </div>
                        </div>

                        <div class="row justify-content-center mb-2">
                            @foreach ($images->where('type', 'newsletter') as $img)
                                <div class="col-md-6 ">
                                    <div class="card image-wrapper position-relative">
                                        <button data-endpoint="{{ route('home.destroy', $img->id) }}"
                                            data-source="image"
                                            class="delete-btn btn btn-danger btn-sm py-0 px-1 position-absolute top-0 end-0 m-2"
                                            title="Delete" style="z-index: 10;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <img src="{{ asset($img->image_path) }}" class="card-img-top fixed-image"
                                            alt="Newsletter Image">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row justify-content-center mb-2">
                            <div class="col-md-6 text-center">
                                <form action="{{ route('images.upload') }}" method="POST" enctype="multipart/form-data"
                                    id="newsletterForm">
                                    @csrf
                                    <input type="file" name="newsletter_image" id="newsletterInput" accept="image/*"
                                        style="display: none">
                                    <button type="button" class="btn btn-primary btn-sm" id="uploadNewsletterBtn">
                                        Upload Newsletter Image
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#uploadSliderBtn').click(() => $('#sliderInput').click());
        $('#sliderInput').change(() => $('#sliderForm').submit());
        $('#uploadNewsletterBtn').click(() => $('#newsletterInput').click());
        $('#newsletterInput').change(() => $('#newsletterForm').submit());
        let $dragged = null;
        $(document).on('dragstart', '.draggable-image', function (e) {
            $dragged = $(this);
            e.originalEvent.dataTransfer.setData('text/plain', 'dragging');
        });
        $(document).on('dragover', '.draggable-image', function (e) {
            e.preventDefault();
        });

        $(document).on('drop', '.draggable-image', function (e) {
            e.preventDefault();
            if ($dragged && !$dragged.is(this)) {
                const src1 = $dragged.attr('src');
                const src2 = $(this).attr('src');
                $dragged.attr('src', src2);
                $(this).attr('src', src1);
            }
        });
    });
</script>
<style>
    .fixed-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        cursor: grab;
        transition: transform 0.2s ease-in-out;
    }
    .fixed-image:active {
        cursor: grabbing;
        transform: scale(0.97);
    }

    .image-wrapper {
        position: relative;
    }

    .delete-btn {
        z-index: 10;
    }
</style>
@endsection
