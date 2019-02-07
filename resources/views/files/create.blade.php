@extends('layouts.app')

@section('content')

    <div class="container-fluid" id="preload-container">
        <div class="row mt-2">
            <div class="col-lg-5 col-sm-12 ">
                <h2>
                    Geolocalizar Arquivo

                    <a href="{{ route('files.index') }}" class="btn btn-sm btn-outline-secondary pull-right">
                        <span class="hidden-sm-up">Voltar</span>
                        <span class="fa fa-undo"></span>
                    </a>
                </h2>

                <form action="{{ route('files.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" name="geocode_file" class="custom-file-input"
                                   accept="application/gzip|text/csv" id="geocode_file">
                            <label class="custom-file-label form-control-file" for="geocode_file">Selecione um
                                arquivo...</label>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-8 col-sm-12">
                            <div class="form-group">
                                <div class="card">
                                    <div class="card-header">
                                        Campos para adicionar ao final de cada linha
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($fields as $field)
                                                <div class="custom-control custom-checkbox d-inline-block mr-2 ml-2">
                                                    <input type="checkbox" name="fields[]"
                                                           class="custom-control-input"
                                                           value="{{ $field }}"
                                                           id="{{ $field }}_check"
                                                            {{ in_array($field, $default) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $field }}_check">
                                                        {{ ucfirst(trans("validation.attributes.$field")) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="delimiter">Delimitador de Colunas:</label>
                                <select name="delimiter" id="delimiter" class="form-control">
                                    <option value="," selected>Vírgula</option>
                                    <option value=";">Ponto-Vírgula</option>
                                    <option value="&Tab;">Tabulação</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="header" value="1" class="custom-control-input"
                                           id="headerCheck" {{ old('header') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="headerCheck">Possui cabeçalho</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="indexes" value="">
                                <input type="hidden" name="count" value="0">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-send-o mr-2"></i> Enviar
                                </button>
                            </div>
                        </div>

                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <div class="card">
                                    <div class="card-header">
                                        Provedores
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($providers as $provider)
                                                <div class="custom-control custom-checkbox d-inline-block mr-2 ml-2">
                                                    <input type="checkbox" name="providers[]"
                                                           class="custom-control-input"
                                                           value="{{ $provider }}"
                                                           id="{{ $provider }}_check"
                                                           checked>
                                                    <label class="custom-control-label" for="{{ $provider }}_check">
                                                        {{ ucfirst(trans("validation.attributes.$provider")) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>

                <div class="row">
                    <div class="col-md-12 text-center">
                        @if($errors->any())
                            <div class="form-control-feedback text-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card mb-2">
                            <div class="card-header">
                                Informações
                            </div>
                            <div class="card-body">
                                <small>Preço:</small>
                                <h4>
                                    US$ <span class="price-value">0.00</span><br/>
                                    <small class="text-muted">US$ (0.50 / 1,000) x <span class="providers-count">4</span> provedores</small>
                                </h4>
                                <hr>
                                <small>Tempo estimado para conclusão:</small>
                                <h4><span class="time-value">-</span> minutos</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-sm-12">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Pré-visualização</div>
                        <div class="card-body">
                            <div class="preview-container-hide">
                                Selecione um arquivo CSV
                            </div>

                            <div class="preview-container" style="display: none">
                                <div class="card example-container mb-2" style="display: none">
                                    <div class="card-body">
                                        <div class="h5">Endereço encontrados:</div>
                                        <table class="table table-sm example-table">
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <div class="row">
                                        <label class="text-muted">
                                            Selecione a(s) coluna(s) com o campo:
                                        </label>
                                    </div>
                                    <div class="row">
                                        <div class="custom-control custom-radio mr-2">
                                            <input type="radio" id="modeRadioAddress" name="mode"
                                                   class="custom-control-input"
                                                   checked>
                                            <label class="custom-control-label" for="modeRadioAddress">Endereço</label>
                                        </div>
                                        <div class="custom-control custom-radio mr-2">
                                            <input type="radio" id="modeRadioLocality" name="mode"
                                                   class="custom-control-input">
                                            <label class="custom-control-label" for="modeRadioLocality">Cidade</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="modeRadioCEP" name="mode"
                                                   class="custom-control-input">
                                            <label class="custom-control-label" for="modeRadioCEP">CEP</label>
                                        </div>
                                    </div>

                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm result-table mt-4">

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function () {
            initPreload();
        });
    </script>
@endsection
