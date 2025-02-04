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

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
    {{-- <div class="logo-container">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div> --}}
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="message-box _success">
                <i class="fa fa-check-circle"></i>
                <h2>Utkarsh Product Verified</h2>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <h2 class="text-center">Beam Details</h2>
        
        <div class="card p-4 shadow-lg">
            <table class="table table-bordered">
                <tr><th>ID</th><td>{{ $beam->id }}</td></tr>
                <tr><th>Grade of Steel</th><td>{{ $beam->grade ?? 'N/A' }}</td></tr>
                <tr><th>Batch Number</th><td>{{ $beam->batch_no ?? 'N/A' }}</td></tr>
                <tr><th>Serial Number</th><td>{{ $beam->serial_no ?? 'N/A' }}</td></tr>
                <tr><th>Origin</th><td>{{ $beam->origin ?? 'N/A' }}</td></tr>
                <tr><th>Asp</th><td>{{ $beam->asp ?? 'N/A' }}</td></tr>
                <tr><th>Created At</th><td>{{ $beam->created_at }}</td></tr>
                <tr><th>Updated At</th><td>{{ $beam->updated_at }}</td></tr>
            </table>

            <div class="text-center mt-3">
                <a href="{{ route('download.pdf', ['filename' => 'w-beam' . '.pdf']) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> Download PDF
                </a>
                
        </div>

    </div>
</body>
</html>
