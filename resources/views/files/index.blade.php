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
                            <th>{{ __('File') }}</th>
                            @can('view', GeoLV\User::class)
                                <th>{{ __('Autor') }}</th>
                            @endcan
                            <th>{{ __('Created at') }}</th>
                            <th width="300px">{{ __('Status') }}</th>
                            <th style="min-width: 150px;">{{ __('Priority') }}</th>
                            <th style="min-width: 150px;">{{ __('Remove') }}</th>
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
                                @include('files.status')
                                <td>
                                    @if(!$file->done)
                                        @can('prioritize', \GeoLV\GeocodingFile::class)
                                            <form action="{{ route('files.prioritize', $file->id) }}" method="post"
                                                  class="d-inline-block">
                                                @csrf

                                                <div class="input-group input-group-sm mb-3">
                                                    <input type="number" class="form-control" name="priority" value="{{ $file->priority }}" min="0" step="1"/>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fa fa-arrow-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endcan
                                    @else
                                        {{ $file->priority }}
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('files.destroy', $file->id) }}" method="post"
                                          class="d-inline-block">
                                        @csrf

                                        <input type="hidden" name="_method" value="DELETE"/>
                                        <button type="submit" class="btn btn-outline-danger" data-toggle="tooltip"
                                                data-placement="right" title="{{ __('Remove file') }}">
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