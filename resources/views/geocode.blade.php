@extends('layout')

@section('content')

    <div class="container">
        <div class="row justify-content-center" style="margin: {{ $results->count() == 0? 200: 50 }}px 0 0 0">
            <div class="col-lg-8 col-md-10 col-xs-12">
                <h1>GeoLV</h1>
                <form action="{{ route('geocode') }}" method="get">
                    <div class="row text-center">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-danger col-md-7" name="text"
                                   placeholder="Endereço"
                                   value="{{ old('text') ?? $text ?? "" }}" tabindex="1" autocomplete="off" autofocus>
                            <input type="text" class="form-control form-control-danger col-md-2" name="locality"
                                   placeholder="Cidade"
                                   value="{{ old('locality') ?? $locality ?? "" }}" tabindex="2" list="localities">
                            <input type="text" class="form-control form-control-danger col-md-3" name="postal_code"
                                   placeholder="CEP"
                                   value="{{ old('postal_code') ?? $postalCode ?? "" }}" tabindex="3" autocomplete="off">

                            <datalist id="localities">
                                @foreach ($localities as $local)
                                    <option value="{{ $local->name }}">
                                @endforeach
                            </datalist>

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
                        @if(filled($text))
                            Endereço: <span class="badge badge-secondary">{{ $text }}</span><br/>
                        @endif

                        @if(filled($locality))
                            Cidade: <span class="badge badge-secondary">{{ $locality }}</span><br/>
                        @endif

                        @if(filled($postalCode))
                            CEP: <span class="badge badge-secondary">{{ $postalCode }}</span><br/>
                        @endif
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
                            <li class="list-group-item list-group-item-default">
                                Classificação: {{ $loop->iteration }}º
                                <a href="#info-algorithm-{{ $result->id }}" class="card-link" style="float: right"
                                   data-toggle="collapse" aria-expanded="false" aria-controls="info-algorithm-{{ $result->id }}">
                                    <i class="fa fa-tasks"></i>
                                </a>
                            </li>
                        </ul>
                        <div class="collapse" id="info-algorithm-{{ $result->id }}">
                            <ul class="list-group list-group-flush">
                                @foreach ($result->algorithm as $key => $value)
                                    @if (filled($value))
                                        <li class="list-group-item">
                                            @if (starts_with($key, 'contains_') || starts_with($key, 'match_') )
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                           id="customCheck1" {{ ($value > 0? 'checked': '') }} disabled>
                                                    <label class="custom-control-label" for="customCheck1">
                                                        <small>
                                                            {{ trans("validation.attributes.$key") }}
                                                            @if($key == "match_locality")
                                                                ({{ $result->locality }})
                                                            @endif
                                                        </small>
                                                    </label>
                                                </div>
                                            @else
                                                <small>{{ trans("validation.attributes.$key") }}:</small>
                                                <div class="progress">
                                                    <div class="progress-bar bg-{{ $value > 0 ? 'success' : 'danger' }}"
                                                         style="width: {{ $value }}%">
                                                        <small>{{ number_format($value, 1) }}%</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item list-group-item-{{ $result->relevance > 0? 'default' : 'danger' }} text-center">
                                <a href="#info-{{ $result->id }}" class="card-link mr-2" data-toggle="collapse" aria-expanded="false" aria-controls="info-{{ $result->id }}">
                                    <i class="fa fa-bars mr-1"></i>Abrir Resultado
                                </a>
                                <a href="{{ route('map', ['selected_id' => $result->id, 'search_id' => $result->search_id ]) }}" class="card-link">
                                    <i class="fa fa-map mr-1"></i> Ver no mapa
                                </a>
                            </li>
                        </ul>
                        <div class="collapse" id="info-{{ $result->id }}">
                            <ul class="list-group list-group-flush">
                                @foreach ($result->fields as $key => $value)
                                    @if (filled($value))
                                        <li class="list-group-item">
                                            <span class="badge mr-1">{{ trans("validation.attributes.$key") }}:</span>
                                            {{ $value }}
                                        </li>
                                    @endif
                                @endforeach
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