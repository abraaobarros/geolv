@extends('layouts.app')

@section('content')

    <div class="container" id="preload-container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-xs-12 mt-2">
                <h2>
                    Geolocalizar Arquivo

                    <a href="{{ route('files.index') }}" class="btn btn-sm btn-outline-secondary ml-2" tabindex="4">
                        <span class="hidden-sm-up">Voltar</span>
                        <span class="fa fa-undo"></span>
                    </a>
                </h2>

                <form action="{{ route('files.store') }}" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" name="geocode_file" class="custom-file-input"
                                   accept="application/gzip|text/csv" id="geocode_file">
                            <label class="custom-file-label form-control-file" for="geocode_file">Selecione um
                                arquivo...</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="header" value="1" class="custom-control-input"
                                   id="headerCheck" {{ old('header') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="headerCheck">Possui cabeçalho</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="card">
                            <div class="card-header">
                                Campos para adicionar ao final de cada linha
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($fields as $field)
                                        <div class="col-md-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="fields[]" class="custom-control-input"
                                                       value="{{ $field }}"
                                                       id="{{ $field }}_check"
                                                        {{ in_array($field, $default) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="{{ $field }}_check">
                                                    {{ trans("validation.attributes.$field") }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <input type="hidden" name="indexes" value="">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-send-o mr-2"></i> Enviar
                        </button>
                    </div>

                    <div class="form-group text-center">
                        @if($errors->any())
                            <div class="form-control-feedback text-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif
                    </div>

                </form>
            </div>
        </div>
        <div class="row preview-container" style="display: none">
            <div class="col-md-12">
                <div class="card mt-5">
                    <div class="card-header">Pre-visualização</div>
                    <div class="card-body">

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
                                    <input type="radio" id="modeRadioAddress" name="mode" class="custom-control-input"
                                           checked>
                                    <label class="custom-control-label" for="modeRadioAddress">Endereço</label>
                                </div>
                                <div class="custom-control custom-radio mr-2">
                                    <input type="radio" id="modeRadioLocality" name="mode" class="custom-control-input">
                                    <label class="custom-control-label" for="modeRadioLocality">Cidade</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="modeRadioCEP" name="mode" class="custom-control-input">
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

@endsection
