<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-danger text-center">
        <h2>File Not Found</h2>
        <p>The requested file <strong>{{ $filename }}</strong> could not be found.</p>
        <a href="/" class="btn btn-primary mt-3">Go Home</a>
    </div>
</div>
</body>
</html>
