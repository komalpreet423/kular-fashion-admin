<!DOCTYPE html>
<html>
<head>
    <title>Filtered Data Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

    </style>
</head>
<body>
    <h2>Filtered Data Report</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->brand->name ?? 'N/A' }}</td>
                <td>{{ $item->category->name ?? 'N/A' }}</td>
                <td>{{ $item->created_at->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
