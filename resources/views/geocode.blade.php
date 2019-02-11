@extends('layouts.app')

@section('content')

    <div class="container">
        <form action="{{ route('geocode') }}" method="get">
            <div class="row justify-content-center" style="margin: {{ $results->count() == 0? 200: 50 }}px 0 0 0">
                <div class="col-lg-8 col-md-10 col-xs-12">
                    <h1>GeoLV</h1>
                    <div class="row text-center">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-danger col-md-7" name="text"
                                   placeholder="Endereço"
                                   value="{{ old('text') ?? $text ?? "" }}" tabindex="1" autocomplete="off" autofocus>
                            <input type="text" class="form-control form-control-danger col-md-3" name="locality"
                                   placeholder="Cidade - UF"
                                   value="{{ old('locality') ?? $locality ?? "" }}" tabindex="2" list="localities">
                            <input type="text" class="form-control form-control-danger col-md-2" name="postal_code"
                                   placeholder="CEP"
                                   value="{{ old('postal_code') ?? $postalCode ?? "" }}" tabindex="3"
                                   autocomplete="off">
                        </div>

                        <datalist id="localities">
                            @foreach ($localities as $local)
                                <option value="{{ $local->full_name }}"></option>
                            @endforeach
                        </datalist>

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
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-xs-12">
                    <a data-toggle="collapse" href="#collapseProviders" role="button" aria-expanded="false"
                       aria-controls="collapseProviders">
                        {{ __('Select providers') }} <i class="fa fa-chevron-down ml-1"></i>
                    </a>
                    <div class="collapse" id="collapseProviders">
                        <div class="card card-body">
                            @foreach ($providers as $provider)
                                <div class="custom-control custom-checkbox d-inline-block mr-2 ml-2">
                                    <input type="checkbox" name="providers[]"
                                           class="custom-control-input"
                                           value="{{ $provider }}"
                                           id="{{ $provider }}_check"
                                            {{ in_array($provider, $selectedProviders) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="{{ $provider }}_check">
                                        {{ ucfirst(trans("validation.attributes.$provider")) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @if (filled($results))
            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5>Busca</h5>
                    <p class="text-muted">
                        @if(filled($text))
                            Endereço: <b>{{ $text }}</b><br/>
                        @endif

                        @if(filled($locality))
                            Cidade: <b>{{ $locality }}</b><br/>
                        @endif

                        @if(filled($postalCode))
                            CEP: <b>{{ $postalCode }}</b>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Algoritmo</h5>
                    <div class="card mb-4">
                        <ul class="list-group list-group-compact list-group-flush">
                            <li class="list-group-item">Dispersão: <b>{{ $dispersion }}</b></li>
                            <li class="list-group-item">
                                Confiança: <b>{{ number_format($confidence, 1) }}<small>/10</small></b> scores
                                <a href="#collapseConfidence" class="small" data-toggle="collapse">
                                    (Mais informações
                                    <i class="fa fa-chevron-down"></i>)
                                </a>
                            </li>
                        </ul>
                        <div class="collapse" id="collapseConfidence">
                            <ul class="list-group list-group-compact list-group-flush">
                                <li class="list-group-item">
                                    <code>
                                        10 <small>(pontuação inicial)</small><br>
                                        @foreach($confidenceInfo as $description => $value)
                                            @if ($value >= 0)
                                                - {{ abs($value) }}
                                            @else
                                                + {{ abs($value) }}
                                            @endif
                                            <small>({{ $description }})</small><br>
                                        @endforeach
                                    </code>
                                </li>
                            </ul>
                        </div>
                        <ul class="list-group list-group-compact list-group-flush">
                            <li class="list-group-item">Quantidade de Clusters: <b>{{ $clustersCount }}</b></li>
                            <li class="list-group-item">Quantidade de Provedores no Cluster Principal:
                                <b>{{ $providersCount }}</b></li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($outside) && filled($outside))
            <div class="row text-center mt-4">
                <div class="col-md-12 text-center">
                    <span class="text-muted">
                        Encontramos <b>{{ $outside->count() }}</b> resultados fora do município.
                    </span>
                </div>
            </div>
        @endif

        <div class="row">
            @foreach ($results as $result)
                <div class="col-lg-4 col-md-12">
                    @include('addresses.item')
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