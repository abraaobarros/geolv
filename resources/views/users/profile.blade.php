@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mt-md-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Profile') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="post">
                            @csrf

                            <div class="form-group">
                                <label for="nameInput">{{ __('Name') }}</label>
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="nameInput" name="name" placeholder="Nome"
                                       value="{{ old('name', $user->name) }}">
                                @include('components.input-error', ['input' => 'name'])
                            </div>

                            <div class="form-group">
                                <label for="emailInput">{{ __('Email') }}</label>
                                <input type="email" class="form-control" id="emailInput" readonly
                                       value="{{ $user->email }}">
                            </div>
                            <hr>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#google-maps" role="tab"
                                       aria-controls="home" aria-selected="true">
                                        Google Maps
                                        @if (filled($user->google_maps_api_key))
                                            <i class="fa fa-check text-success ml-2"></i>
                                        @else
                                            <i class="fa fa-close text-danger ml-2"></i>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#here-geocoder" role="tab"
                                       aria-controls="profile" aria-selected="false">
                                        Here Geocoder
                                        @if (filled($user->here_geocoder_code) && filled($user->here_geocoder_id))
                                            <i class="fa fa-check text-success ml-2"></i>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#bing-maps" role="tab"
                                       aria-controls="contact" aria-selected="false">
                                        Bing Maps
                                        @if (filled($user->bing_maps_api_key))
                                            <i class="fa fa-check text-success ml-2"></i>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-2" id="myTabContent">
                                <div class="tab-pane fade show active" id="google-maps" role="tabpanel">

                                    <div class="form-group">
                                        <label for="googleApiKeyInput">API Key</label>
                                        <input type="text" class="form-control{{ $errors->has('google_maps.api_key') ? ' is-invalid' : '' }}" id="googleApiKeyInput"
                                               name="google_maps[api_key]" value="{{ $user->google_maps_api_key }}">
                                        @include('components.input-error', ['input' => 'google_maps.api_key'])
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="here-geocoder" role="tabpanel">

                                    <div class="form-group">
                                        <label for="hereGeocoderId">ID</label>
                                        <input type="text" class="form-control" id="hereGeocoderId"
                                               name="here_geocoder[here_id]" value="{{ $user->here_geocoder_id }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="hereGeocoderCode">Code</label>
                                        <input type="text" class="form-control" id="hereGeocoderCode"
                                               name="here_geocoder[code]" value="{{ $user->here_geocoder_code }}">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="bing-maps" role="tabpanel">

                                    <div class="form-group">
                                        <label for="bingMapsApiKey">API Key</label>
                                        <input type="text" class="form-control" id="bingMapsApiKey"
                                               name="bing_maps[api_key]" value="{{ $user->bing_maps_api_key }}">
                                    </div>

                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save mr-2"></i>
                                {{ __('Save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection