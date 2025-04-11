@extends('front.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-9 pt-4">
        <div class="container">
            <form id="orderForm" name="orderForm" method="post" action="">
                <div class="row">
                    <div class="col-md-8">
                        <div class="sub-title">
                            <h2>Information</h2>
                        </div>
                        <div class="card shadow-lg border-0">
                            <div class="card-body checkout-form">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="email" id="email" class="form-control" placeholder="Email"
                                                   value="{{ (!empty($default_email)) ? $default_email : '' }}">
                                            <p></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile No.">
                                            <p></p>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-title">
                            <h3>Order Summery</h3>
                        </div>
                        <div class="card cart-summery">
                            <div class="card-body">
                                @foreach(Cart::content() as $item)
                                    <div class="d-flex justify-content-between pb-2">
                                        <div class="h6">{{ $item->name }}</div>
                                        <div class="h6">{{ number_format($item->price, 0, ',', '.') }} </div>
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-between mt-2 summery-end">
                                    <div class="h5"><strong>Total</strong></div>
                                    <div class="h5"><strong>{{ number_format((float) str_replace(',', '', Cart::subtotal()), 0, ',', '.') }} đ</strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="pt-4">
                                <button type="submit" class="btn-dark btn btn-block w-100">Pay with MoMo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('customJS')
    <script>
        $("#orderForm").submit(function(event) {
            event.preventDefault();

            $('button[type="submit"]').prop('disabled', true);

            $.ajax({
                url: '{{ route('front.preparePayment') }}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    first_name: $('#first_name').val(),
                    last_name: $('#last_name').val(),
                    email: $('#email').val(),
                    mobile: $('#mobile').val(),
                },
                dataType: 'json',
                success: function(response) {
                    var errors = response.errors;
                    $('button[type="submit"]').prop('disabled', false);

                    if (response.status == false) {
                        if (errors.first_name) {
                            $("#first_name").addClass('is-invalid').siblings("p").addClass('invalid-feedback').html(errors.first_name)
                        } else {
                            $("#first_name").removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html('')
                        }

                        if (errors.last_name) {
                            $("#last_name").addClass('is-invalid').siblings("p").addClass('invalid-feedback').html(errors.last_name)
                        } else {
                            $("#last_name").removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html('')
                        }

                        if (errors.email) {
                            $("#email").addClass('is-invalid').siblings("p").addClass('invalid-feedback').html(errors.email)
                        } else {
                            $("#email").removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html('')
                        }

                        if (errors.mobile) {
                            $("#mobile").addClass('is-invalid').siblings("p").addClass('invalid-feedback').html(errors.mobile)
                        } else {
                            $("#mobile").removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html('')
                        }
                    } else {
                        if (response.payUrl) {
                            window.location.href = response.payUrl;
                        } else {
                            $('button[type="submit"]').prop('disabled', false);
                            alert("Something went wrong.");
                        }
                    }
                }
            });
        });
    </script>
@endsection

