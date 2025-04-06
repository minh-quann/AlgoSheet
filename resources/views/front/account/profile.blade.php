@extends('front.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-11">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-3">
                    @include('front.account.common.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" 
                                           value="{{ old('name', $user->name ?? '') }}" 
                                           placeholder="Enter Your Name" class="form-control" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" 
                                           value="{{ old('email', $user->email ?? '') }}" 
                                           placeholder="Enter Your Email" class="form-control" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" 
                                           value="{{ old('phone', $user->phone ?? '') }}" 
                                           placeholder="Enter Your Phone" class="form-control" disabled>
                                </div>

                                <div class="mb-3">
                                    <label for="address">Address</label>
                                    <textarea name="address" id="address" class="form-control" cols="30" rows="5" 
                                              placeholder="Enter Your Address" disabled>{{ old('address', $user->address ?? '') }}</textarea>
                                </div>

                                <!-- Nút Chỉnh sửa Profile -->
                                {{-- <div class="d-flex">
                                    <a href="{{ route('account.editProfile') }}" class="btn btn-primary">Edit Profile</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
