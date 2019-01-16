@if($file->initializing)
    <td>{{ __('On queue...') }}</td>
    <td>
        <a href="{{ request()->url() }}" class="btn btn-block btn-outline-success">
            {{ __('Refresh') }} <i class="fa fa-refresh"></i>
        </a>
    </td>
@elseif($file->done)
    <td>{{ __('Finished') }} {{ $file->updated_at->diffForHumans($file->created_at) }}</td>
    <td>
        <a href="{{ route('files.show', $file->id) }}" class="btn btn-block btn-outline-success">
            <i class="fa fa-download mr-2"></i>
            {{ __('Download') }}
        </a>
    </td>
@else
    <td>
        <small>{{ __('Processing') }}: {{ number_format($file->progress, 1) }}%</small>
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: {{ $file->progress }}%"></div>
        </div>
    </td>
    <td>
        <a href="{{ route('files.show', $file->id) }}" class="btn btn-block btn-outline-warning">
            <i class="fa fa-download mr-2"></i>
            {{ __('Download') }} <b>{{ __('partial') }}</b>
        </a>
    </td>
@endif