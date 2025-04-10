@extends('front.layouts.app')

@section('content')
    <section class="container">
        <div class="col-md-12 text-center py-5">
            @if (Session::has('error'))
                <div class="alert alert-danger">
                    {{ Session::get('error') }}
                </div>
            @endif
            <h1 class="text-danger">Thanh toán không thành công!</h1>
            <p>Rất tiếc, thanh toán của bạn không thành công hoặc đã bị huỷ.</p>
            <p>{{ request()->query('message') ?? 'Vui lòng thử lại sau hoặc sử dụng phương thức thanh toán khác.' }}</p>
            <p>Your Order ID is: {{ $id }}</p>
            <a href="{{ route('front.cart') }}" class="btn btn-primary mt-3">Return to Cart</a>
        </div>
    </section>
@endsection
