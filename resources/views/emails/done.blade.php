@component('mail::message')
# {{ __('Process finished') }}

{{ __('Your file has been successfully processed!') }}

@component('mail::button', ['url' => route('files.show', $file->id)])
{{ __('Download file') }}
@endcomponent
@endcomponent