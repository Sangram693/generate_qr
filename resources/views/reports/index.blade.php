<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reports - QR Generator</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
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
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            border: 1px solid #e5e7eb;
            height: calc(1.8em + 0.8rem);
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.3rem;
        }

        .mb-3 {
            margin-bottom: 0.8rem !important;
        }

        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 0.4rem 1rem;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .stats-card {
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .status-badge {
            font-size: 0.875rem;
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
        }

        .bulk-actions {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/generate') }}">
                <img src="{{ url('UT LOGO.png') }}" alt="UT Logo">
                Report
            </a>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    <i class="fas fa-user me-1"></i>
                    Welcome, {{ auth()->user()->name }}
                </span>
                <a href="{{ url('/generate') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-qrcode me-1"></i> Generate
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

    <div class="main-content container">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Total Reports</h5>
                        <h3>{{ $stats['total_reports'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">Pending Deliveries</h5>
                    <h3>{{ $stats['pending_deliveries'] }}</h3>
                </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Delivered</h5>
                    <h3>{{ $stats['delivered'] }}</h3>
                </div>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" action="{{ route('reports.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Date Range</label>
                            <div class="input-group">
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                                <span class="input-group-text">to</span>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Product</label>
                            <select name="product_name" class="form-select form-select-sm">
                                <option value="">All Products</option>
                                <option value="w-beam" {{ request('product_name') == 'w-beam' ? 'selected' : '' }}>W-Beam</option>
                                <option value="pole" {{ request('product_name') == 'pole' ? 'selected' : '' }}>Pole</option>
                                <option value="high-mast" {{ request('product_name') == 'high-mast' ? 'selected' : '' }}>High Mast</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions mb-3" style="display:none;">
            <form id="bulkUpdateForm" action="{{ route('reports.bulk-update') }}" method="POST">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Update Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-save me-1"></i> Update Selected
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reports Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <h5 class="mb-0">Reports List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Generation Date</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input report-checkbox" 
                                            name="report_ids[]" value="{{ $report->id }}"
                                            {{ $report->delivery_date ? 'disabled' : '' }}>
                                    </td>
                                    <td>{{ $report->id }}</td>
                                    <td>{{ ucfirst($report->product_name) }}</td>
                                    <td>{{ $report->qty }}</td>
                                    <td>{{ $report->formatted_generation_date }}</td>
                                    <td>{{ $report->formatted_delivery_date }}</td>
                                    <td>
                                        <span class="badge {{ $report->delivery_date ? 'bg-success' : 'bg-warning' }} status-badge">
                                            {{ $report->delivery_date ? 'Delivered' : 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">No reports found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select all checkbox functionality
            $('#selectAll').change(function() {
                $('.report-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
                updateBulkActionsVisibility();
            });

            // Individual checkboxes
            $('.report-checkbox').change(function() {
                updateBulkActionsVisibility();
            });

            // Update bulk actions visibility
            function updateBulkActionsVisibility() {
                const checkedCount = $('.report-checkbox:checked').length;
                $('.bulk-actions')[checkedCount > 0 ? 'show' : 'hide']();
            }

            // Add selected IDs to form before submit
            $('#bulkUpdateForm').submit(function() {
                $('.report-checkbox:checked').each(function() {
                    $(this).clone().attr('type', 'hidden').appendTo('#bulkUpdateForm');
                });
            });
        });
    </script>
</body>

</html>
