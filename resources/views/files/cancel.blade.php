<form action="{{ route('files.cancel', $file->id) }}" method="post" class="d-inline-block ml-1">
    @csrf

    @if ($file->canceled_at)
        <button type="submit" class="btn btn-block btn-outline-success" data-toggle="tooltip"
                data-placement="right" title="{{ __('Resume file') }}">
            <i class="fa fa-play"></i>
        </button>
    @else
        <button type="submit" class="btn btn-block btn-outline-secondary" data-toggle="tooltip"
                data-placement="right" title="{{ __('Cancel file') }}">
            <i class="fa fa-pause"></i>
        </button>
    @endif
</form>