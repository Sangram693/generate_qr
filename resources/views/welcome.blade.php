<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generate QR Code PDF</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .main-content {
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .card-header {
            background-color: #4f46e5;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.3rem;  /* Reduced margin below labels */
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.4rem 0.75rem;  /* Reduced from 0.75rem */
            border: 1px solid #e5e7eb;
            height: calc(1.8em + 0.8rem); /* Added for consistent height */
        }

        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }

        .mb-3 {
            margin-bottom: 0.8rem !important;  /* Reduced spacing between form groups */
        }

        .card-body {
            padding: 1.5rem !important;  /* Reduced padding */
        }

        .btn {
            padding: 0.4rem 1rem;  /* Reduced button padding */
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/generate') }}">
                <img src="{{ url('UT LOGO.png') }}" alt="UT Logo">
                QR Generator
            </a>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    <i class="fas fa-user me-1"></i>
                    Welcome, {{ auth()->user()->name }}
                </span>
                <a href="{{ url('/reports') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-chart-bar me-1"></i> Reports
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Generate QR Code PDF</h2>
            </div>
            <div class="card-body p-4">
                <form id="generate-form" action="{{ route('pages.store') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="page_height" class="form-label">Page Height (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="page_height" name="page_height" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="page_width" class="form-label">Page Width (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="page_width" name="page_width" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="margin_top" class="form-label">Top Margin (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="margin_top" name="margin_top" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="margin_bottom" class="form-label">Bottom Margin (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="margin_bottom" name="margin_bottom" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="margin_left" class="form-label">Left Margin (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="margin_left" name="margin_left" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="margin_right" class="form-label">Right Margin (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="margin_right" name="margin_right" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qr_height" class="form-label">QR Height (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="qr_height" name="qr_height" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="qr_width" class="form-label">QR Width (inches)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm" id="qr_width" name="qr_width" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="row_number" class="form-label">Number of QR Codes</label>
                            <input type="number" class="form-control form-control-sm" id="row_number" name="row_number" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="product_type" class="form-label">Product Type</label>
                            <select class="form-select form-select-sm" id="product_type" name="product_type" required>
                                <option value="">Select Product Type</option>
                                <option value="w-beam">W-Beam</option>
                                <option value="pole">Pole</option>
                                <option value="high-mast">High Mast</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-qrcode me-2"></i> Generate QR Code PDF
                    </button>
                </form>
            </div>
        </div>

        <!-- Response Box -->
        <div id="response-box" class="mt-4 card" style="display: none;">
            <div class="card-body">
                <div id="response-message" class="alert mb-3"></div>
                <div id="download-links"></div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and other scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#generate-form').on('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                var formData = {};
                $(this).serializeArray().forEach(function(item) {
                    formData[item.name] = item.value;
                });
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Generating...'
                );
                
                $.ajax({
                    url: '/pages',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Show success message
                        $('#response-box').show();
                        $('#response-message').removeClass().addClass('alert alert-success').html('QR codes generated successfully! Downloading PDF...');
                        
                        // Extract filename from pdf_url
                        var pdfUrl = new URL(response.pdf_url);
                        var pdfFile = pdfUrl.pathname.split('/').pop();
                        
                        // Create temporary link for download
                        var downloadLink = document.createElement('a');
                        downloadLink.href = '/download/pdf/' + pdfFile;
                        downloadLink.setAttribute('download', response.pdf_name + '.pdf');
                        document.body.appendChild(downloadLink);
                        
                        // Trigger click and remove link
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                        
                        // Hide response box after 3 seconds
                        setTimeout(function() {
                            $('#response-box').fadeOut();
                        }, 3000);
                    },
                    error: function(xhr) {
                        $('#response-box').show();
                        $('#response-message').removeClass().addClass('alert alert-danger');
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            $('#response-message').html(xhr.responseJSON.message);
                        } else {
                            $('#response-message').html('An error occurred while generating QR codes.');
                        }
                        
                        $('#download-links').empty();
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false).html(
                            '<i class="fas fa-qrcode me-2"></i> Generate QR Code PDF'
                        );
                    }
                });
            });
        });
    </script>
</body>

</html>
