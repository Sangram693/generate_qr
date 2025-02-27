<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High Mast Details</title>

    <!-- Bootstrap for Styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>

    @if(!empty($highmast->batch_no))
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="message-box _success">
                <i class="fa fa-check-circle"></i>
                <h2>Utkarsh Product Verified</h2>
            </div>
        </div>
    </div>
    @endif
    <div class="container mt-5">
        <h2 class="text-center">High Mast Details</h2>
        
        <div class="card p-4 shadow-lg">
            <table class="table table-bordered">
                <tr><th>ID</th><td>{{ $highmast->id }}</td></tr>
                <tr><th>Grade of Steel</th><td>{{ $highmast->grade ?? 'N/A' }}</td></tr>
                <tr><th>Batch Number</th><td>{{ $highmast->batch_no ?? 'N/A' }}</td></tr>
                <tr><th>Serial Number</th><td>{{ $highmast->serial_no ?? 'N/A' }}</td></tr>
                <tr><th>Origin</th><td>{{ $highmast->origin ?? 'N/A' }}</td></tr>
                <tr><th>Asp</th><td>{{ $highmast->asp ?? 'N/A' }}</td></tr>
                <tr><th>Created At</th><td>{{ $highmast->created_at }}</td></tr>
                <tr><th>Updated At</th><td>{{ $highmast->updated_at }}</td></tr>
            </table>

            
        </div>
    </div>
</body>
</html>
