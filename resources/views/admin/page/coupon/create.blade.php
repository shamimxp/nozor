@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add Coupon</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.coupon.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Coupon Code</label>
                            <input type="text" name="code" class="form-control" required value="{{ old('code') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Percentage</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required value="{{ old('amount') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ old('start_date') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" required value="{{ old('end_date') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save Coupon</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
