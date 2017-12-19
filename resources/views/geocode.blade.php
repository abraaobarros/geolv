@extends('layout')

@section('content')

    <div class="container">
        <div class="row justify-content-center" style="margin: {{ $results->count() == 0? 200: 50 }}px 0 0 0">
            <div class="col-lg-8 col-md-10 col-xs-12">
                <h1>GeoLV</h1>
                <form action="{{ url('/geocode') }}" method="get">
                    <div class="row text-center">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-danger col-md-7" name="street_name"
                                   placeholder="Endereço"
                                   value="{{ old('street_name') ?? $street_name ?? "" }}" tabindex="1" autofocus>
                            <input type="text" class="form-control form-control-danger col-md-2" name="locality"
                                   placeholder="Cidade"
                                   value="{{ old('locality') ?? $locality ?? "" }}" tabindex="2">
                            <input type="text" class="form-control form-control-danger col-md-3" name="cep"
                                   placeholder="CEP"
                                   value="{{ old('cep') ?? $cep ?? "" }}" tabindex="3">
                        </div>
                        @if($errors->any())
                            <div class="form-control-feedback text-danger">{{ $errors->first() }}</div>
                        @endif
                    </div>
                    <div class="row justify-content-center" style="margin-top: 20px">
                        <button type="submit" class="btn btn-outline-primary" tabindex="4">
                            <span class="hidden-sm-up">Pesquisar</span>
                            <span class="fa fa-search"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($results->count() > 0)
            <hr>

            <div class="row">
                <div class="col-md-12">
                    <p>
                        Procurando por: <span class="badge badge-secondary">{{ ucwords($match) }}</span>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" id="geolv-container">
                    <div class="geolv-map"
                         data-center="{{ $results->getCenter()->getLatitude() . ',' . $results->getCenter()->getLongitude() }}"
                         style="width: 100%; height: 500px">
                    </div>

                    @foreach ($results as $result)
                        <div class="geolv-result"
                             data-id="{{ $result->id }}"
                             data-street-name="{{ $result->street_name }}"
                             data-number="{{ $result->street_number }}"
                             data-locality="{{ $result->locality }}"
                             data-sub-locality="{{ $result->sub_locality }}"
                             data-country-code="{{ $result->country_code }}"
                             data-country-name="{{ $result->country_name }}"
                             data-provider="{{ $result->provider }}"
                             data-latitude="{{ $result->latitude }}"
                             data-longitude="{{ $result->longitude }}"
                             data-relevance="{{ $result->relevance }}"></div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
    <script type="text/javascript">
        $('.collapse').collapse({toggle: false});
    </script>
@endsection