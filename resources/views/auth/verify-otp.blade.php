@extends('/layouts/auth')

@push('css-dependencies')
<link href="/css/auth.css" rel="stylesheet" />
@endpush

@section("content")
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">{{ $title }} Page</h1>
                                </div>

                                @if(session()->has('message'))
                                {!! session("message") !!}
                                @endif

                                <form class="user" method="post" action="{{ url('/auth/verify-otp') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form-control @error('otp') is-invalid @enderror"
                                          id="otp" name="otp" placeholder="Enter OTP" required>
                                        @error('otp')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-info btn-block">
                                        Verify
                                    </button>
                                </form>
                                <hr>

                                <div class="text-center">
                                    <a class="small" href="/auth/login">Back to Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
