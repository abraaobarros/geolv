@component('mail::message')
# {{ __('Process canceled') }}

{{ __('Could not process your file.') }}

@component('mail::button', ['url' => route('files.show', $file->id)])
    {{ __('Download') }} <b>{{ __('partial') }}</b>
@endcomponent

@if($receiver->isDev())
#### Error:
> {{ $error }}
@endif
@endcomponent