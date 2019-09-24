@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ __('Register') }}
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary pull-right" tabindex="4">
                            <span class="hidden-sm-up">{{ __('Go Back') }}</span>
                            <span class="fa fa-undo"></span>
                        </a>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                           name="name" value="{{ old('name') }}" autofocus>
                                    @include('components.input-error', ['input' => 'name'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" value="{{ old('email') }}">
                                    @include('components.input-error', ['input' => 'email'])
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label for="googleApiKeyInput"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Google API Key') }}</label>

                                <div class="col-md-6">
                                    <input type="text"
                                           class="form-control{{ $errors->has('google_maps.api_key') ? ' is-invalid' : '' }}"
                                           id="googleApiKeyInput"
                                           name="google_maps[api_key]">
                                    @include('components.input-error', ['input' => 'google_maps.api_key'])
                                    <a class="form-text small text-muted text-right" target="_blank" href="https://maplink.global/blog/como-obter-chave-api-google-maps/">
                                        <i class="fa fa-external mr-2"></i> Não tenho uma Google API Key
                                    </a>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           name="password">
                                    @include('components.input-error', ['input' => 'password'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="password_confirmation">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
