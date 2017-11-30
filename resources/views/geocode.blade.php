@extends('layout')

@section('content')

    <div class="row justify-content-center" style="margin-top: {{ $results->count() == 0? 200: 50 }}px">
        <div class="col-md-8 col-sm-12">
            <h1>GeoLV</h1>
            <form action="{{ url('/geocode') }}" method="get">
                <div class="form-group">
                    <div class="input-group">
                        <label for="address" class="sr-only">Endereço</label>
                        <input type="text" class="form-control form-control-danger col-9" name="street_name"
                               placeholder="R. Exemplo, 8, Bairro"
                               value="{{ old('street_name') ?? $street_name ?? "" }}" tabindex="1" autofocus>
                        <input type="text" class="form-control form-control-danger col-3" name="cep" placeholder="CEP"
                               value="{{ old('cep') ?? $cep ?? "" }}" tabindex="2">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary" tabindex="3">
                                <span class="fa fa-search"></span>
                            </button>
                        </div>
                    </div>
                    @if($errors->any())
                        <div class="form-control-feedback text-danger">{{ $errors->first() }}</div>
                    @endif
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
                            Relevância: {{ $result->relevance }}
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
                            <li class="list-group-item">País: ({{ $result->country_code }}) {{ $result->country_name }}</li>
                            <li class="list-group-item">Latitude: {{ $result->latitude }}</li>
                            <li class="list-group-item">Longitude: {{ $result->longitude }}</li>
                            <li class="list-group-item">CEP: {{ $result->postal_code }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $('.collapse').collapse({toggle: false});
    </script>
@endsection