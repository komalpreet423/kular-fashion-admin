@extends('layouts.app')

@section('title', 'Categories')
@section('header-button')
    <a href="{{ url('download-category-sample') }}" class="btn btn-primary primary-btn btn-md me-2">
        <i class="bx bx-download"></i> Download Brands Sample
    </a>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bx bx-plus"></i> Add New Category
    </a>
@endsection
@section('content')
<style>
.container-fluid {
    padding: 25px;
    border: 1px solid #00000042;
    border-radius: 10px;
    background: white;
}
button.delete-btn
{
    border: unset;
    color: red;
    background: #ffffff;
}
</style>
<div class="page-content">
    <div class="container-fluid">
        <h4>Category List</h4>
        <ul class="list-unstyled">
            @foreach ($categories as $category)
                @include('categories.partials.category-item', ['category' => $category, 'level' => 0])
            @endforeach
        </ul>
    </div>
</div>

{{-- Modal for Adding Category --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addCategoryForm" method="POST" action="{{ route('categories.store') }}">
            @csrf
            <input type="hidden" name="parent_id" id="parentCategoryId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" name="name" id="categoryName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="categoryImage" class="form-label">Image</label>
                        <input type="file" name="image" id="categoryImage" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.add-subcategory-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                let categoryId = this.getAttribute('data-id');
                document.getElementById('parentCategoryId').value = categoryId;
                let modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
                modal.show();
            });
        });
    });
</script>
@endpush
