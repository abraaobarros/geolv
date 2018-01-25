@component('mail::message')
# CSV Pronto!

O seu arquivo foi processo com sucesso!

@component('mail::button', ['url' => route('files.show', $file->id)])
    Clique para baixar
@endcomponent

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent