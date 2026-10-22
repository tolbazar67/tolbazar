<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Branch - TolBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Branch Outlet
                        </h4>
                        <a href="{{ route('seller.branches.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('seller.branches.update', $branch->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Branch Name *</label>
                            <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name', $branch->branch_name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Branch Phone Number *</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Branch Full Address *</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $branch->address) }}" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('seller.branches.index') }}" class="btn btn-light border px-4">Cancel</a>
                            <button type="submit" class="btn btn-dark px-4 shadow-sm fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Update Branch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>