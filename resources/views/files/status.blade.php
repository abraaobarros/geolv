@if($file->initializing)
    @if ($file->canceled_at)
        {{ __('Canceled') }} {{ $file->canceled_at->diffForHumans($file->created_at) }}
    @else
        {{ __($file->in_process ? 'Initializing' : 'On queue') }}...
    @endif
@elseif($file->done)
    {{ __('Finished') }} {{ $file->updated_at->diffForHumans($file->created_at) }}
@else
    @if ($file->canceled_at)
        <small>
            {{ __('Canceled') }}:
            ({{$file->offset }} / {{ $file->count }})
        </small>
    @else
        <small>
            {{ __($file->in_process ? 'Processing' : 'Paused (On queue)') }}:
            ({{$file->offset }} / {{ $file->count }})
        </small>
    @endif
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: {{ $file->progress }}%">
            {{ number_format($file->progress, 1) }}%
        </div>
    </div>
@endif