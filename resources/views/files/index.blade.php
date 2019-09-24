@extends('layouts.app')

@section('content')

    <div class="container-fluid mt-md-5">

        <div class="row justify-content-center">

            <div class="col-md-12">

                <h2>{{ __('Files') }}</h2>

                @include('components.google-warning')

                <a href="{{ route('files.create') }}" class="btn btn-primary">
                    <span class="fa fa-file mr-2"></span>
                    Geolocalizar novo arquivo
                </a>

                <a href="{{ request()->fullUrl() }}" class="btn btn-outline-success">
                    <i class="fa fa-refresh mr-2"></i> Atualizar
                </a>

                <table class="table table-hover mt-4">
                    <thead>
                    <tr>
                        <th>{{ __('File') }}</th>
                        @can('view', GeoLV\User::class)
                            <th style="min-width: 100px;">{{ __('Autor') }}</th>
                        @endcan
                        <th class="d-none d-md-block">{{ __('Created at') }}</th>
                        @can('prioritize', \GeoLV\GeocodingFile::class)
                            <th width="100px">{{ __('Priority') }}</th>
                        @endcan
                        <th width="300px">{{ __('Status') }}</th>
                        <th style="min-width: 200px">{{ __('Actions') }}</th>
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
                            <td class="text-truncate" style="max-width: 250px; font-size: 0.8em">{{ $file->name }}</td>
                            @can('view', GeoLV\User::class)
                                <td><a href="{{ route('users.show', $file->user_id) }}">{{ $file->user->name }}</a></td>
                            @endcan
                            <td class="d-none d-md-block">{{ $file->created_at->diffForHumans() }}</td>
                            @can('prioritize', \GeoLV\GeocodingFile::class)
                                <td>
                                    @include('files.priority')
                                </td>
                            @endcan
                            <td>
                                @include('files.status')
                            </td>
                            <td>
                                @include('files.actions')
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