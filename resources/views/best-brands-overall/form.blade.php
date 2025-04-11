<div class="row mb-3">
    <div class="col-md-2">
        <label for="branch_id">Branch</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">Select Branch</option>
            @foreach ($branches as $branch)
            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label for="departments" class="form-label">Departments</label>
        <select class="form-select" id="departments" name="department_id">
            <option value="">Select Department</option>
            @foreach($departments as $department)
            <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label for="productTypes" class="form-label">Product Types</label>
        <select class="form-select" id="productTypes" name="product_type_id">
            <option value="">Select Product Type</option>
            @foreach($productTypes as $productType)
            <option value="{{ $productType->id }}">{{ $productType->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label for="seasons" class="form-label">Seasons</label>
        <select class="form-select" id="seasons" name="season">
            <option value="">Select Season</option>
            <option value="Summer">Summer</option>
            <option value="Winter">Winter</option>
            <option value="Autumn">Autumn</option>
            <option value="Spring">Spring</option>
        </select>
    </div>
    <div class="col-md-2">
        <x-form-input name="from_date" class="best-brand-date-picker" value="" label="From date" placeholder="From date" />
    </div>
    <div class="col-md-2">
        <x-form-input name="to_date" class="best-brand-date-picker" value="" label="To date" placeholder="To date" />
    </div>
</div>

<div class="row mb-3 align-items-end">

    <!-- Show Options -->
    <div class="col-lg-4">
        <label class="form-label d-block">Show Options</label>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div>
                <input type="radio" class="btn-check" name="show_options" id="allLines" value="all" autocomplete="off" checked>
                <label class="btn btn-outline-secondary" for="allLines">All Lines</label>
            </div>
            <div>
                <input type="radio" class="btn-check" name="show_options" id="firstLines" value="first" autocomplete="off">
                <label class="btn btn-outline-secondary" for="firstLines">Only Show First</label>
            </div>
            <div>
                <input type="number" class="form-control" style="width: 100px;" name="first_lines_count" placeholder="Enter number" disabled>
            </div>
        </div>
    </div>

    <!-- Sorting Sequence -->
    <div class="col-lg-4">
        <label class="form-label d-block">Sorting Sequence</label>
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="sequence" id="ascending" value="ascending" autocomplete="off">
            <label class="btn btn-outline-primary" for="ascending">Ascending</label>

            <input type="radio" class="btn-check" name="sequence" id="descending" value="descending" autocomplete="off">
            <label class="btn btn-outline-primary" for="descending">Descending</label>
        </div>
    </div>

    <!-- Buttons -->
    <div class="col-lg-4 d-flex align-items-end justify-content-end">
        <div>
            <button type="button" class="btn btn-primary me-2" id="applyFilterBtn">Apply Filter</button>
            <a href="{{ route('best-brands-overall.index') }}" class="btn btn-secondary">
                <i class="bx bx-reset"></i> Reset
            </a>
        </div>
    </div>

</div>