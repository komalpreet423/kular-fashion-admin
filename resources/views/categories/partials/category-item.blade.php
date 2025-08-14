<li style="width:{{ 100 - $level * 1 }}%; margin-left:{{ $level * 1 }}%;">
    <div
        style="border:1px solid #00000030; border-radius:6px; background:#f6f6f9; margin:5px 0; padding:8px; display:flex; justify-content:space-between; align-items:center;">
        <span>: : {{ $category->name }}</span>
        <span>
            <a href="{{ config('app.frontend_url') }}/Category/{{ $category->slug }}"
                class="btn btn-info btn-sm py-0 px-1" title="View category" target="_blank" aria-label="View category">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('categories.edit', $category->id) }}"><i class="fa fa-pencil"></i></a> |
            <button data-source="Category" data-endpoint="{{ route('categories.destroy', $category->id) }}"
                class="delete-btn">
                <i class="fas fa-trash-alt"></i>
            </button>
            <a href="javascript:void(0);" class="add-subcategory-btn" data-id="{{ $category->id }}">
                <i class="fa fa-plus"></i>
            </a>
        </span>
    </div>

    @if ($category->childrenRecursive->count())
        <ul style="list-style:none; padding-left:0;">
            @foreach ($category->childrenRecursive as $child)
                @include('categories.partials.category-item', [
                    'category' => $child,
                    'level' => $level + 1,
                ])
            @endforeach
        </ul>
    @endif
</li>
