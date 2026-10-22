<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Seller - TolBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center py-5">

    <div class="card shadow-lg p-4 border-0" style="width: 600px;">
        <h3 class="fw-bold mb-2 text-center">Open Your Store & Setup Branch</h3>
        <p class="text-muted text-center mb-4">Fill in your business and primary branch details to start selling.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('seller.store') }}" method="POST">
            @csrf
            
            <!-- Store / Business Section -->
            <h5 class="text-dark fw-bold mb-3 border-bottom pb-2">🏢 Store / Business Information</h5>
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Store / Business Name *</label>
                <input type="text" name="store_name" class="form-control" value="{{ old('store_name') }}" placeholder="e.g. Kathmandu Electronics" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Business Phone *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. 98XXXXXXXX" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">PAN / VAT Number</label>
                    <input type="text" name="pan_vat_number" class="form-control" value="{{ old('pan_vat_number') }}" placeholder="Optional">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Head Office Address *</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="e.g. New Road, Kathmandu" required>
            </div>

            <!-- Initial Branch Section (Default Outlet) -->
            <h5 class="text-dark fw-bold mt-4 mb-3 border-bottom pb-2">📍 Initial Branch Setup (Default Outlet)</h5>

            <div class="mb-3">
                <label class="form-label fw-semibold">Branch Name *</label>
                <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name') }}" placeholder="e.g. Main Outlet - New Road" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Branch Phone *</label>
                    <input type="text" name="branch_phone" class="form-control" value="{{ old('branch_phone') }}" placeholder="e.g. 98XXXXXXXX" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Branch Address *</label>
                    <input type="text" name="branch_address" class="form-control" value="{{ old('branch_address') }}" placeholder="e.g. Store No. 12, New Road" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold mt-3">Register Store & Branch</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted">← Back to Home</a>
        </div>
    </div>

</body>
</html>