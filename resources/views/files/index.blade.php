@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center mt-md-2">

            <div class="col-md-12">

                <a href="{{ route('files.create') }}" class="btn btn-primary">
                    <span class="fa fa-file mr-2"></span>
                    Geolocalizar novo arquivo
                </a>

                <table class="table mt-4">
                    <thead>
                    <tr>
                        <th>Arquivo</th>
                        <th>Linhas Processadas</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($files as $file)
                    <tr>
                        <td>{{ $file->path }}</td>
                        <td>{{ $file->offset }}</td>
                        <td>
                            @if($file->initializing)
                                <i class="fa fa-spinner fa-pulse fa-fw text-success mr-2"></i>
                                Processando...
                            @elseif($file->done)
                                <a href="{{ route('files.show', $file->id) }}" class="btn btn-outline-success">
                                    <i class="fa fa-download mr-2"></i>
                                    Baixar
                                </a>
                            @else
                                <a href="{{ route('files.show', $file->id) }}" class="btn btn-outline-warning">
                                    <i class="fa fa-spinner fa-pulse fa-fw text-warning mr-2"></i>
                                    Baixar <b>parcial</b>
                                </a>
                            @endif
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