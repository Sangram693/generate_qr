<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pole Details</title>

    <!-- Bootstrap for Styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    @if(!empty($pole->batch_no))
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
        <h2 class="text-center">Pole Details</h2>
        
        <div class="card p-4 shadow-lg">
            <table class="table table-bordered">
                <tr><th>Serial Number</th><td>{{ $pole->id }}</td></tr>
                <tr><th>Grade of Steel</th><td>{{ $pole->grade ?? 'N/A' }}</td></tr>
                <tr><th>Batch Number</th><td>{{ $pole->batch_no ?? 'N/A' }}</td></tr>
                <tr><th>Origin</th><td>{{ $pole->origin ?? 'N/A' }}</td></tr>
                <tr><th>Asp</th><td>{{ $pole->asp ?? 'N/A' }}</td></tr>
                <tr><th>Created At</th><td>{{ $pole->created_at }}</td></tr>
                <tr><th>Updated At</th><td>{{ $pole->updated_at }}</td></tr>
            </table>

            
        </div>
    </div>
</body>
</html>
