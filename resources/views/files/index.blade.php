@extends('layouts.app')

@section('content')

    <div class="container-fluid mt-md-5">

        <div class="row justify-content-center">

            <div class="col-md-12">

                <h2>{{ __('Files') }}</h2>

                <a href="{{ route('files.create') }}" class="btn btn-primary">
                    <span class="fa fa-file-o mr-2"></span>
                    Geolocalizar novo arquivo
                </a>

                <a href="{{ request()->url() }}" class="btn btn-outline-success">
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
                                <th style="min-width: 100px;">{{ __('Autor') }}</th>
                            @endcan
                            <th>{{ __('Created at') }}</th>
                            @can('prioritize', \GeoLV\GeocodingFile::class)
                                <th style="max-width: 100px;">{{ __('Priority') }}</th>
                            @endcan
                            <th width="300px">{{ __('Status') }}</th>
                            <th style="min-width: 160px;">{{ __('Actions') }}</th>
                            <th>{{ __('Remove') }}</th>
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
                                @can('prioritize', \GeoLV\GeocodingFile::class)
                                    <td>
                                        @if(!$file->done)
                                            <form action="{{ route('files.prioritize', $file->id) }}" method="post"
                                                  class="d-inline-block">
                                                @csrf

                                                <div class="input-group input-group-sm mb-3">
                                                    <input type="number" class="form-control" name="priority"
                                                           value="{{ $file->priority }}" min="0" step="1"/>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit">
                                                            <i class="fa fa-arrows-v"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @else
                                            <div class="input-group input-group-sm mb-3">
                                                <input type="number" class="form-control" name="priority"
                                                       value="{{ $file->priority }}" min="0" step="1" readonly/>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary disabled" disabled>
                                                        <i class="fa fa-arrows-v"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endcan
                                @include('files.status')
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