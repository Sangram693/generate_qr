<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beam Details</title>

    <!-- Bootstrap for Styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Beam Details</h2>
        
        <div class="card p-4 shadow-lg">
            <table class="table table-bordered">
                <tr><th>ID</th><td>{{ $beam->id }}</td></tr>
                <tr><th>Name</th><td>{{ $beam->name ?? 'N/A' }}</td></tr>
                <tr><th>Description</th><td>{{ $beam->description ?? 'N/A' }}</td></tr>
                <tr><th>Model Number</th><td>{{ $beam->model_number ?? 'N/A' }}</td></tr>
                <tr><th>Serial Number</th><td>{{ $beam->serial_number ?? 'N/A' }}</td></tr>
                <tr><th>Batch Number</th><td>{{ $beam->bach_number ?? 'N/A' }}</td></tr>
                <tr><th>Manufacturer</th><td>{{ $beam->manufacturer ?? 'N/A' }}</td></tr>
                <tr><th>Type</th><td>{{ $beam->beam_type ?? 'N/A' }}</td></tr>
                <tr><th>Shape</th><td>{{ $beam->beam_shape ?? 'N/A' }}</td></tr>
                <tr><th>Length</th><td>{{ $beam->beam_length ?? 'N/A' }} cm</td></tr>
                <tr><th>Width</th><td>{{ $beam->beam_width ?? 'N/A' }} cm</td></tr>
                <tr><th>Height</th><td>{{ $beam->beam_height ?? 'N/A' }} cm</td></tr>
                <tr><th>Weight</th><td>{{ $beam->beam_weight ?? 'N/A' }} kg</td></tr>
                <tr><th>Created At</th><td>{{ $beam->created_at }}</td></tr>
                <tr><th>Updated At</th><td>{{ $beam->updated_at }}</td></tr>
            </table>

            
        </div>
    </div>
</body>
</html>
