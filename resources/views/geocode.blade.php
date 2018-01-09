@extends('layout')

@section('content')

    <div class="container">
        <div class="row justify-content-center" style="margin: {{ $results->count() == 0? 200: 50 }}px 0 0 0">
            <div class="col-lg-8 col-md-10 col-xs-12">
                <h1>GeoLV</h1>
                <form action="{{ route('geocode') }}" method="get">
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
                        <div class="btn-group">
                            <button type="submit" class="btn btn-outline-primary" tabindex="4">
                                <span class="hidden-sm-up">Pesquisar</span>
                                <span class="fa fa-search"></span>
                            </button>
                            <a href="{{ route('geocode.file') }}" class="btn btn-outline-secondary" tabindex="4">
                                <span class="hidden-sm-up">Geolocalizar arquivo</span>
                                <span class="fa fa-file"></span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($results->count() > 0)
            <hr>

            <div class="row">
                <div class="col-md-12">
                    <p>
                        Procurando por: <span class="badge badge-secondary">{{ ucwords($search->text) }}</span>
                    </p>
                </div>
            </div>
        @endif

        <div class="row">
            @foreach ($results as $result)
                <div class="col-lg-4 col-md-12">
                    <div class="card" style="margin-bottom: 10px">
                        <div class="card-body">
                            <h4 class="card-title">{{ $result->street_name }}</h4>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $result->provider }}</h6>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item list-group-item-{{ $result->relevance > 0? 'default' : 'danger' }}">
                                <a href="{{ route('map', ['selected_id' => $result->id, 'search_id' => $search->id ]) }}" class="card-link">
                                    Ver no mapa
                                </a>
                            </li>
                            <li class="list-group-item list-group-item-{{ $result->relevance > 0? 'default' : 'danger' }}">
                                Relevância: {{ $result->relevance }}%
                                <a href="#info-{{ $result->id }}" class="card-link" style="float: right"
                                   data-toggle="collapse" aria-expanded="false" aria-controls="info-{{ $result->id }}">
                                    Mais
                                </a>
                            </li>
                        </ul>
                        <div class="collapse" id="info-{{ $result->id }}">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Número: {{ $result->street_number }}</li>
                                <li class="list-group-item">Localidade: {{ $result->locality }}</li>
                                <li class="list-group-item">Sub-localidade: {{ $result->sub_locality }}</li>
                                <li class="list-group-item">País: ({{ $result->country_code }}
                                    ) {{ $result->country_name }}</li>
                                <li class="list-group-item">Latitude: {{ $result->latitude }}</li>
                                <li class="list-group-item">Longitude: {{ $result->longitude }}</li>
                                <li class="list-group-item">CEP: {{ $result->postal_code }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $('.collapse').collapse({toggle: false});
    </script>
@endsection