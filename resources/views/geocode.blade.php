@extends('layouts.app')

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
                            <input type="text" class="form-control form-control-danger col-md-3" name="locality"
                                   placeholder="Cidade"
                                   value="{{ old('locality') ?? $locality ?? "" }}" tabindex="2" list="localities">
                            <input type="text" class="form-control form-control-danger col-md-2" name="postal_code"
                                   placeholder="CEP"
                                   value="{{ old('postal_code') ?? $postalCode ?? "" }}" tabindex="3" autocomplete="off">
                        </div>

                        <datalist id="localities">
                            @foreach ($localities as $local)
                                <option value="{{ $local->name }}">
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
                </form>
            </div>
        </div>

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
                    <p class="text-muted">
                        Dispersão: <b>{{ $dispersion }}</b><br/>
                        Quantidade de Clusters: <b>{{ $clustersCount }}</b><br/>
                        Quantidade de Provedores no Cluster Principal: <b>{{ $providersCount }}</b>
                    </p>
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