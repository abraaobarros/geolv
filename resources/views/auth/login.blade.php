@extends('layouts.app', ['fullscreen' => true])

@section('content')
<div class="container">
    <div class="row justify-content-center mt-md-5">
        <div class="col-lg-4 col-md-6">
            <h1 class="text-center">{{ config('app.name', 'GeoLV') }}</h1>
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body padding-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">

                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif

                        </div>

                        <div class="form-group">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif

                            <div class="help-block text-right">
                                <a href="{{ url('password/reset') }}" class="btn btn-link btn-sm">{{ __('Forgot Your Password?') }}</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="remember" class="custom-control-input"
                                       id="rememberCheck" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="rememberCheck">{{ __('Remember Me') }}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-sign-in mr-2"></i>{{ __('Login') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center">
                <a class="btn btn-link" href="{{ route('register') }}">
                    Não possui conta? Cadastre-se!
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
