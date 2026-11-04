@extends('layouts.mypanel')
@section('title', 'My Profile')

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Profile</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('mypanel.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Edit Profile</li>
    </ol>
</div>
@endsection

@section('content')
@if(session()->has('admin_id'))
    <div class="alert alert-warning">
        <div class="alert-body d-flex justify-content-between align-items-center">
            <span>You are currently logged in as Worker (Impersonation Mode)</span>
            <a href="{{ route('mypanel.back') }}" class="btn btn-sm btn-dark">Back to Admin</a>
        </div>
    </div>
@endif
<section id="page-account-settings">
    <div class="row">
        <div class="col-md-4 mb-2">
            <div class="card">
                <div class="card-body text-center">
                    @if($worker->profile_image)
                        <img src="{{ asset('storage/'.$worker->profile_image) }}" class="rounded-circle mb-1" width="100" height="100" style="object-fit:cover;" alt="Profile">
                    @else
                        <div class="rounded-circle mb-1 bg-light d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px;">
                            <i data-feather="user" style="width:40px;height:40px;color:#aaa;"></i>
                        </div>
                    @endif
                    <h4 class="mb-0">{{ $worker->name }}</h4>
                    <p class="text-muted mb-0">{{ $worker->type_label }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-2">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Profile Details</h4>
                </div>
                <div class="card-body py-2 my-25">
                    <form class="validate-form mt-2 pt-50" action="{{ route('mypanel.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $worker->name) }}" required>
                                    @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $worker->phone) }}" required>
                                    @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $worker->email) }}">
                                    @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="profile_image">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                    @error('profile_image')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $worker->address) }}</textarea>
                                    @error('address')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="password">New Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mt-1">Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
