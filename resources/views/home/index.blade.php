@extends('layouts.app')
@section('title', 'Home')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                {{-- Error & Success Messages --}}
                <x-error-message :message="$errors->first('message')" />
                <x-success-message :message="session('success')" />

                <div class="card shadow rounded">
                    <div class="card-body">

                        <div class="row mb-4 text-center">
                            <div class="col-12">
                                <h2>Upload Images</h2>
                            </div>
                        </div>

                        <div class="row mb-4 text-center">
                            @forelse ($images as $img)
                                <div class="col-md-4 mb-3">
                                    <div class="card image-wrapper position-relative">
                                        <button
                                            data-endpoint="{{ route('home.destroy', $img->id) }}"
                                            class="delete-btn btn btn-danger btn-sm py-0 px-1 position-absolute top-0 end-0 m-2"
                                            title="Delete"
                                            style="z-index: 10;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        {{-- Show the image --}}
                                        <img src="{{ asset($img->image_path) }}"
                                             class="card-img-top fixed-image draggable-image"
                                             alt="Uploaded Image"
                                             draggable="true">
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p>No images uploaded yet.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-6 text-center">
                                <form action="{{ route('images.upload') }}" method="POST" enctype="multipart/form-data" id="imageForm">
                                    @csrf
                                    <input type="file" name="images[]" id="imageInput" accept="image/*" multiple style="display: none">
                                    <button type="button" class="btn btn-primary" id="selectUploadBtn">Select & Upload Images</button>
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
        $('#selectUploadBtn').on('click', function () {
            $('#imageInput').click();
        });

        $('#imageInput').on('change', function () {
            $('#imageForm').submit();
        });

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
                let src1 = $dragged.attr('src');
                let src2 = $(this).attr('src');
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
