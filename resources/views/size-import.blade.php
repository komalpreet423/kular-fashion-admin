<div class="container">
    <h2>Import Size Codes</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Upload Form --}}
    <form action="{{ route('import.size.codes') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="file" class="form-label">Select Excel File</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls,.csv" required>
        </div>

        <button type="submit" class="btn btn-primary">Import</button>
    </form>
</div>