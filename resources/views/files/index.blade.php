@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center mt-md-5">

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

                <div class="table-responsive">
                    <table class="table table-hover mt-4">
                        <thead>
                        <tr>
                            <th>Arquivo</th>
                            @can('view', GeoLV\User::class)
                            <th>Autor</th>
                            @endcan
                            <th>Criado</th>
                            <th width="300px">Status</th>
                            <th>Ações</th>
                            <th>Remover</th>
                        </tr>
                        </thead>
                        <tbody>

                        @if(blank($files))
                            <tr>
                                <td colspan="5">Nenhum arquivo processado.</td>
                            </tr>
                        @endif

                        @foreach($files as $file)
                            <tr>
                                <td><span class="badge badge-default">{{ $file->file_name }}</span></td>
                                @can('view', GeoLV\User::class)
                                <td>{{ $file->user->name }}</td>
                                @endcan
                                <td>{{ $file->created_at->diffForHumans() }}</td>
                                @if($file->initializing)
                                    <td>Inicializando...</td>
                                    <td>
                                        <a href="{{ request()->url() }}" class="btn btn-block btn-outline-success">
                                            Atualizar <i class="fa fa-refresh"></i>
                                        </a>
                                    </td>
                                @elseif($file->done)
                                    <td>Finalizado {{ $file->updated_at->diffForHumans($file->created_at) }}</td>
                                    <td>
                                        <a href="{{ route('files.show', $file->id) }}" class="btn btn-block btn-outline-success">
                                            <i class="fa fa-download mr-2"></i>
                                            Baixar
                                        </a>
                                    </td>
                                @else
                                    <td>
                                        <small>Processando: {{ number_format($file->progress, 1) }}%</small>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $file->progress }}%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('files.show', $file->id) }}" class="btn btn-block btn-outline-warning">
                                            <i class="fa fa-download mr-2"></i>
                                            Baixar <b>parcial</b>
                                        </a>
                                    </td>
                                @endif
                                <td>
                                    <form action="{{ route('files.destroy', $file->id) }}" method="post">
                                        @csrf

                                        <input type="hidden" name="_method" value="DELETE"/>
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

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