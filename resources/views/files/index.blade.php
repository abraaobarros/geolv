@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center mt-md-2">

            <div class="col-md-12">

                <a href="{{ route('files.create') }}" class="btn btn-primary">
                    <span class="fa fa-file mr-2"></span>
                    Geolocalizar novo arquivo
                </a>

                <a href="{{ request()->url() }}" class="pull-right btn btn-outline-success">
                    <i class="fa fa-refresh mr-2"></i> Atualizar
                </a>

                @if(session()->has('upload'))
                    <div class="alert alert-success mt-2" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        Seu arquivo está sendo processado. Enviaremos um email para {{ auth()->user()->email }} quando
                        concluído.
                    </div>
                @endif

                <table class="table table-hover table-bordered mt-4">
                    <thead>
                    <tr>
                        <th>Arquivo</th>
                        <th>Linhas Processadas</th>
                        <th>Criado</th>
                        <th data-toggle="tooltip" data-title="endereços / segundo">Velocidade
                            <small>(end./s)</small>
                        </th>
                        <th>Tempo total de processamento</th>
                        <th>Ações</th>
                        <th>Remover</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(blank($files))
                        <tr>
                            <td colspan="7">Nenhum arquivo processado.</td>
                        </tr>
                    @endif

                    @foreach($files as $file)
                        <tr>
                            <td><span class="badge badge-default">{{ $file->file_name }}</span></td>
                            <td>{{ $file->offset }}</td>
                            <td>{{ $file->created_at->diffForHumans() }}</td>
                            <td>
                                {{ $file->initializing? '-' : number_format($file->velocity, 2) }}
                            </td>
                            <td>
                                {{ $file->initializing? '-' : $file->updated_at->diffForHumans($file->created_at) }}
                            </td>

                            @if($file->initializing)
                                <td width="162px">
                                    Processando
                                    <a href="{{ request()->url() }}" class="btn btn-sm btn-outline-success ml-2">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </td>
                            @elseif($file->done)
                                <td>
                                    <a href="{{ route('files.show', $file->id) }}" class="btn btn-outline-success">
                                        <i class="fa fa-download mr-2"></i>
                                        Baixar
                                    </a>
                                </td>
                            @else
                                <td>
                                    <a href="{{ route('files.show', $file->id) }}" class="btn btn-outline-warning">
                                        <i class="fa fa-download mr-2"></i>
                                        Baixar <b>parcial</b>
                                    </a>
                                </td>
                            @endif
                            <td>
                                <form action="{{ route('files.destroy', $file->id) }}" method="post">
                                    <input type="hidden" name="_method" value="DELETE"/>
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fa fa-close"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{ $files->links('pagination::bootstrap-4') }}

            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection