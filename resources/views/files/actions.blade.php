<div class="d-flex">
    @if($file->initializing)
        <a href="{{ request()->fullUrl() }}" class="btn btn-outline-success w-100">
            {{ __('Refresh') }} <i class="fa fa-refresh"></i>
        </a>
        <form action="{{ route('files.cancel', $file->id) }}" method="post" class="d-inline-block ml-1">
            @csrf

            @if ($file->canceled_at)
                <button type="submit" class="btn btn-block btn-outline-success" data-toggle="tooltip"
                        data-placement="right" title="{{ __('Resume file') }}">
                    <i class="fa fa-play-circle-o"></i>
                </button>
            @else
                <button type="submit" class="btn btn-block btn-outline-secondary" data-toggle="tooltip"
                        data-placement="right" title="{{ __('Cancel file') }}">
                    <i class="fa fa-ban"></i>
                </button>
            @endif
        </form>
    @elseif($file->done)
        <a href="{{ route('files.show', $file->id) }}" class="btn btn-outline-success w-100">
            <i class="fa fa-download mr-2"></i>
            {{ __('Download') }}
        </a>
    @else
        <a href="{{ route('files.show', $file->id) }}" class="btn btn-outline-warning w-100">
            <i class="fa fa-download mr-2"></i>
            <span class="d-sm-none d-md-inline-block">
                {{ __('Download') }} <b>{{ __('partial') }}</b>
            </span>
        </a>
        <form action="{{ route('files.cancel', $file->id) }}" method="post" class="d-inline-block ml-1">
            @csrf

            @if ($file->canceled_at)
                <button type="submit" class="btn btn-block btn-outline-success" data-toggle="tooltip"
                        data-placement="right" title="{{ __('Resume file') }}">
                    <i class="fa fa-play-circle-o"></i>
                </button>
            @else
                <button type="submit" class="btn btn-block btn-outline-secondary" data-toggle="tooltip"
                        data-placement="right" title="{{ __('Cancel file') }}">
                    <i class="fa fa-ban"></i>
                </button>
            @endif
        </form>
    @endif
    <form action="{{ route('files.destroy', $file->id) }}" method="post" class="ml-1">
        @csrf

        <input type="hidden" name="_method" value="DELETE"/>
        <button type="submit" class="btn btn-outline-danger" data-toggle="tooltip"
                data-placement="right" title="{{ __('Remove file') }}">
            <i class="fa fa-trash-o"></i>
        </button>
    </form>
</div>