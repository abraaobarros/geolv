@component('mail::message')
# {{ __('Process finished') }}

{{ __('Your file has been successfully processed!') }}

@component('mail::button', ['url' => "http://geolv.cepesp.io/files/{$file->id}/download"])
{{ __('Download file') }}
@endcomponent
@endcomponent