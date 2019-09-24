@if ($errors->has($input))
    <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first($input) }}</strong>
    </span>
@endif