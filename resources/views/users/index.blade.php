@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-md-5">
        <div class="row justify-content-center ">
            <div class="col-md-12">

                <h2>{{ __('Users') }}</h2>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('E-Mail Address') }}</th>
                            <th>{{ __('Created at') }}</th>
                            <th style="min-width: 160px;">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    {{ $user->name }}
                                    @if ($user->role)
                                    <span class="badge badge-info badge-pill">{{ mb_strtoupper($user->role) }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $user->email }}
                                    @if(!empty($user->email_verified_at))
                                        <i class="fa fa-check-square-o ml-2 text-success" data-toggle="tooltip" data-placement="right" title="{{ __('Email Verified') }}"></i>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-success mr-2">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    @if ($user->id != auth()->user()->getAuthIdentifier())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="post" class="d-inline-block">
                                        @csrf

                                        <input type="hidden" name="_method" value="DELETE"/>
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                    @else
                                        <button type="submit" class="btn btn-outline-danger disabled" disabled>
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $users->links('pagination::bootstrap-4') }}

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