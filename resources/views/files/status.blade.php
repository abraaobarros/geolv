@if($file->deleted_at)
    {{ __('Deleted') }} {{ $file->deleted_at->diffForHumans($file->created_at) }}
@elseif($file->initializing)
    @if ($file->canceled_at)
        {{ __('Canceled') }} {{ $file->canceled_at->diffForHumans($file->created_at) }}
    @else
        {{ __($file->in_process ? 'Initializing' : 'On queue') }}...
    @endif
@elseif($file->done)
    {{ number_format($file->count, 0, ',', '.') }} {{ __('lines') }}<br/>
    <small>{{ __('Finished') }} {{ $file->updated_at->diffForHumans($file->created_at) }}</small>
@else
    <small>
        @if ($file->canceled_at)
            {{ __('Canceled') }}:
        @else
            {{ __($file->in_process ? 'Processing' : 'Paused (On queue)') }}:
        @endif

        ({{ $file->offset }} / {{ $file->count }})
    </small>

    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: {{ $file->progress * 100 }}%">
            {{ number_format($file->progress * 100, 1) }}%
        </div>
    </div>
@endif