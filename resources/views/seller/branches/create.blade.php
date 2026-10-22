@extends('layouts.seller')
@section('title', 'Add Branch')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">➕ Add New Branch</h4>

                    <form action="{{ route('seller.branches.store') }}" method="POST">
                        @csrf

                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Branch Information</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Branch Name *</label>
                                <input type="text" name="branch_name" class="form-control"
                                       value="{{ old('branch_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address *</label>
                                <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <hr>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="create_manager"
                                   name="create_manager" value="1"
                                   {{ old('create_manager') ? 'checked' : '' }}>
                            <label for="create_manager" class="form-check-label fw-semibold">
                                Create a Manager login for this branch
                            </label>
                        </div>

                        <div id="managerFields" style="display:none;">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Manager Login</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Manager Name *</label>
                                    <input type="text" name="manager_name" class="form-control"
                                           value="{{ old('manager_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Manager Email *</label>
                                    <input type="email" name="manager_email" class="form-control"
                                           value="{{ old('manager_email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password *</label>
                                    <input type="password" name="manager_password" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('seller.branches.index') }}" class="btn btn-light">Cancel</a>
                            <button class="btn btn-primary">Create Branch</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const cb = document.getElementById('create_manager');
    const wrap = document.getElementById('managerFields');
    cb.addEventListener('change', () => {
        wrap.style.display = cb.checked ? 'block' : 'none';
    });
    if (cb.checked) wrap.style.display = 'block';
</script>
@endsection