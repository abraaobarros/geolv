@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mt-md-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Info about :name', ['name' => $user->name]) }}
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary pull-right"
                           tabindex="4">
                            <span class="hidden-sm-up">{{ __('Go Back') }}</span>
                            <span class="fa fa-undo"></span>
                        </a>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">{{ __('Name') }}</dt>
                            <dd class="col-sm-9">
                                {{ $user->name }}
                                @if ($user->role)
                                    <span class="badge badge-info badge-pill">{{ mb_strtoupper($user->role) }}</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">{{ __('E-Mail Address') }}</dt>
                            <dd class="col-sm-9">
                                {{ $user->email }}
                                @if($user->email_verified_at)
                                    <span class="badge badge-success ml-2">
                                    <i class="fa fa-check-square-o mr-2"></i> {{ __('Verified') }}
                                </span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">{{ __('Created at') }}</dt>
                            <dd class="col-sm-9">{{ $user->created_at->diffForHumans() }} ({{ __('Last update')
                                }}: {{ $user->last_update->diffForHumans() }})
                            </dd>

                            <dt class="col-sm-3">{{ __('Total processed lines') }}</dt>
                            <dd class="col-sm-9">
                                {{ number_format($user->total_processed_lines, 0, ',', '.') }} {{ __('lines') }}
                            </dd>

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
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @if(blank($files))
                                    <tr>
                                        <td colspan="5">Nenhum arquivo processado.</td>
                                    </tr>
                                @endif

                                @foreach($files as $file)
                                    <tr class="{{ $file->trashed() ? 'table-disabled' : '' }}">
                                        <td class="text-truncate" style="max-width: 250px; font-size: 0.8em">
                                            @if($file->trashed())
                                                <span class="badge badge-pill badge-dark">{{ __('Deleted') }}</span>
                                            @endif
                                            {{ $file->name }}
                                        </td>
                                        <td>{{ $file->created_at->diffForHumans() }}</td>
                                        <td>@include('files.status')</td>
                                        <td>@include('files.actions')</td>
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

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection