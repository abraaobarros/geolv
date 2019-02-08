@extends('layouts.app', ['fullscreen' => true])

@section('content')

    <a href="{{ route('geocode', $search->toRequestFormat($providers)) }}" class="btn btn-outline-primary" style="position: fixed; top: 10px; left: 10px; z-index: 20">
        Voltar
    </a>

    <div class="card" style="position: fixed; top: 60px; left: 10px; z-index: 20; max-width: 180px">
        <div class="card-header">
            Opções
        </div>
        <div class="card-body">
            <form action="{{ route('map') }}" method="get">
                <label for="max_d">
                    <small>Cluster Max:</small>
                    <input type="number" name="max_d" id="max_d" step="0.001" value="{{ old('max_d', $search->max_d) }}" class="form-control form-control-sm">
                    <input type="hidden" name="selected_id" value="{{ $selected->id }}">
                    <input type="hidden" name="search_id" value="{{ $search->id }}">
                    @foreach($providers as $provider)
                    <input type="hidden" name="providers[]" value="{{ $provider }}"/>
                    @endforeach
                </label>

                <button type="submit" class="btn btn-block btn-outline-success btn-sm"><i class="fa fa-refresh"></i> Atualizar</button>
            </form>
        </div>
    </div>

    <div class="card" style="position: fixed; top: 10px; right: 10px; z-index: 20; max-width: 3000px">
        <div class="card-header">
            Algoritmo
        </div>
        <div class="card-body">
            <p class="text-muted small">
                Dispersão: <b>{{ $dispersion }}</b><br/>
                Precisão: <b>{{ $precision }} m</b><br/>
                Confiança: <b>{{ number_format($confidence * 100, 1) }}%</b><br/>
                Quantidade de Clusters: <b>{{ $clustersCount }}</b><br/>
                Quantidade de Provedores no Cluster Principal: <b>{{ $providersCount }}</b>
            </p>
        </div>
    </div>


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
                 data-cluster="{{ $result->cluster }}"></div>
        @endforeach
    </div>

@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
@endsection