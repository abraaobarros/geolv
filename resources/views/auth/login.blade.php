@extends('layouts.base')

@section('body')
    <div class="container">
        <div class="row justify-content-center mt-md-5">
            <div class="col-lg-4 col-md-6">

                <h1 class="text-center">{{ config('app.name') }}</h1>

                <div class="card">
                    <div class="card-body padding-4">
                        <form method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <input type="email" class="form-control" name="email" id="email"
                                       value="{{ old('email') }}" placeholder="Endereço de E-mail" required autofocus/>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <input type="password" class="form-control" name="password" id="password"
                                       placeholder="Senha" required/>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="remember" class="custom-control-input"
                                           id="rememberCheck" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="rememberCheck">Lembrar de mim</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Entrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center">
                    <a class="btn btn-link" href="{{ route('register') }}">
                        Não possui conta? Cadastre-se!
                    </a>
                    <!--<a class="btn btn-link" href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>-->
                </div>

            </div>
        </div>
    </div>
@endsection
