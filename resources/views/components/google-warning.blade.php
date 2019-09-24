@if(blank(auth()->user()->google_maps_api_key))
    <div class="alert alert-warning mt-2" role="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Cadastre suas credenciais do <a href="{{ route('profile.edit') }}">Google Maps API</a>
    </div>
@endif