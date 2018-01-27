@extends('layouts.base')

@section('body')
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
        <h2 class="my-0 mr-md-4 font-weight-normal">{{ config('app.name') }}</h2>
        <nav class="my-2 my-md-0 mr-md-auto">
            <a class="p-2 text-dark{{ request()->is('/')? ' active': '' }}" href="{{ route('home') }}">Geolocalizar Endereço</a>
            <a class="p-2 text-dark{{ request()->is('/files')? ' active': '' }}" href="{{ route('files.index') }}">Geolocalizar CSV</a>
        </nav>
        <div class="my-2 my-md-0 mr-md-3">
            Olá, {{ auth()->user()->name }}!
        </div>
        <a class="btn btn-outline-primary" href="{{ route('logout') }}"
           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            Sair <i class="fa fa-sign-out ml-2"></i>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>

    @yield('content')
@endsection
