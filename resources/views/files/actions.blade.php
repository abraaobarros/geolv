<div class="d-flex">
    @if($file->initializing)
        <a href="{{ request()->fullUrl() }}" class="btn btn-outline-success w-100">
            {{ __('Refresh') }} <i class="fa fa-refresh"></i>
        </a>
        @include('files.cancel')
    @elseif($file->done)
        <div class="btn-group w-100">
            <a href="{{ route('files.download', $file->id) }}" class="btn btn-outline-success">
                <i class="fa fa-download mr-2"></i>
                <span class="d-sm-none d-md-inline-block">
                {{ __('Download') }}
                </span>
            </a>
            <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="max-width: 50px">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item text-danger" href="{{ route('files.download-errors', $file->id) }}">
                    <i class="fa fa-download mr-2"></i> {{ __('Download results not found') }}
                </a>
                <a class="dropdown-item" href="{{ route('files.map', $file->id) }}">
                    <i class="fa fa-map mr-2"></i> {{ __('Open map') }}
                </a>
            </div>
        </div>
    @else
        <div class="btn-group w-100">
            <a href="{{ route('files.download', $file->id) }}" class="btn btn-outline-warning">
                <i class="fa fa-download mr-2"></i>
                <span class="d-sm-none d-md-inline-block">
                {{ __('Download') }} <b>{{ __('partial') }}</b>
                </span>
            </a>
            <button type="button" class="btn btn-outline-warning dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="max-width: 50px">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item text-danger" href="{{ route('files.download-errors', $file->id) }}">
                    <i class="fa fa-download mr-2"></i> {{ __('Download results not found') }}  <b>({{ __('partial') }})</b>
                </a>
                <a class="dropdown-item" href="{{ route('files.map', $file->id) }}">
                    <i class="fa fa-map mr-2"></i> {{ __('Open map') }}  <b>({{ __('partial') }})</b>
                </a>
            </div>
        </div>
        @include('files.cancel')
    @endif
    @if(!$file->trashed())
    <form action="{{ route('files.destroy', $file->id) }}" method="post" class="ml-1">
        @csrf

        <input type="hidden" name="_method" value="DELETE"/>
        <button type="submit" class="btn btn-outline-danger" data-toggle="tooltip"
                data-placement="right" title="{{ __('Remove file') }}">
            <i class="fa fa-trash-o"></i>
        </button>
    </form>
    @endif
</div>