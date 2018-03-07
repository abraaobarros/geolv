@extends('layouts.base')

@section('body')

    <a href="javascript:history.back()" class="btn btn-outline-primary" style="position: fixed; top: 10px; left: 10px; z-index: 20">Voltar</a>

    <div id="geolv-container" style="position: fixed; width: 100%; height: 100%">
        <div class="geolv-map" style="width: 100%; height: 100%" data-locality="{{ implode('|', $search->findLocality()->getRect()) }}">
        </div>

        @foreach ($results as $result)
            <div class="geolv-result"
                 data-is-focus="{{ $selected->id == $result->id }}"
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
                 data-relevance="{{ $result->relevance }}"
                 data-group="{{ $result->group }}"></div>
        @endforeach
    </div>

@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
@endsection