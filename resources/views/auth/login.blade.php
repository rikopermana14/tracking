@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-header-custom"><center>Aplication Vessel Monitoring PT Logindo Samudramakmur TBK</center></div>

                <div class="card-body">
                    <div class="login-container">
                        <div class="logo">
                            <img src="{{ asset('logo/logo-logindo.png') }}" alt="Logo">
                        </div>
                        <div class="login-form">
                            <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        {{-- <strong>{{ $message }}</strong> --}}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        {{-- <strong>{{ $message }}</strong> --}}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

</div>
<div class="alert alert-danger alert-dismissible fade show fixed-top" role="alert" id="loginAlert" style="display: none;">
    Email or password is incorrect
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<script>
    // Fungsi untuk menampilkan alert melayang
    function showLoginAlert(message) {
        var alert = document.getElementById('loginAlert');
        alert.innerHTML = message;
        alert.style.display = 'block';
        setTimeout(function() {
            alert.style.display = 'none';
        }, 8000); // Sembunyikan alert setelah 5 detik
    }

    // Panggil fungsi untuk menampilkan alert jika terjadi kesalahan login
    @if ($errors->has('email') || $errors->has('password'))
        showLoginAlert("Email or password is incorrect.");
    @endif
</script>



<style>
    .login-container {
        display: flex;
        align-items: center;
    }

    .logo {
        flex: 1;
        padding: 20px;
    }

    .logo img {
        max-width: 100%;
    }

    .login-form {
        flex: 2;
        padding: 20px;
    }

    .card-header-custom {
            height: 50px;
            font-size: 18px; /* Ubah ukuran teks sesuai kebutuhan Anda */
            font-weight: bold; /* Membuat teks menjadi bold */
        }
</style>
@endsection
