@foreach ($categories as $category)
    <option value="{{ $category->id }}" @selected(in_array($category->id, $selectedCategories ?? []))>
        {{ $prefix }}{{ $category->name }}
    </option>

    @if ($category->childrenRecursive->count())
        @include('products.partials.category-dropdown', [
            'categories' => $category->childrenRecursive,
            'prefix' => $prefix . $category->name . ' > ',
            'selectedCategories' => $selectedCategories
        ])
    @endif
@endforeach
