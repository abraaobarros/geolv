@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mt-md-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Info about :name', ['name' => $user->name]) }}
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary pull-right" tabindex="4">
                            <span class="hidden-sm-up">{{ __('Go Back') }}</span>
                            <span class="fa fa-undo"></span>
                        </a>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">{{ __('Name') }}</dt>
                            <dd class="col-sm-9">{{ $user->name }}</dd>

                            <dt class="col-sm-3">{{ __('E-Mail Address') }}</dt>
                            <dd class="col-sm-9">
                                {{ $user->email }}
                                <span class="badge badge-success ml-2">
                                    <i class="fa fa-check-square-o mr-2"></i> {{ __('Verified') }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">{{ __('Created at') }}</dt>
                            <dd class="col-sm-9">{{ $user->created_at->diffForHumans() }} ({{ __('Last update') }}: {{ $user->updated_at->diffForHumans() }})</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-md-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Files of :name', ['name' => $user->name]) }}</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mt-4">
                                <thead>
                                <tr>
                                    <th>Arquivo</th>
                                    <th>Criado</th>
                                    <th width="300px">Status</th>
                                    <th>Ações</th>
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
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $files->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection