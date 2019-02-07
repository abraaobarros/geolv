@component('mail::message')
# {{ __('Process canceled') }}

{{ __('Could not process your file.') }}

@component('mail::button', ['url' => route('files.download', $file->id)])
{{ __('Download') }} <u>{{ __('partial') }}</u>
@endcomponent

@if($receiver->isDev())
#### Error:
@component('mail::panel')
{{ $error }}
@endcomponent
@endif

@endcomponent