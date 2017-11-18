<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

        <title>GeoLV</title>
    </head>
    <body>
        <div class="container">

            <div class="row justify-content-center" style="margin-top: 200px">
                <div class="col-8">
                    <h1>GeoLV</h1>
                    <form action="{{ url('/') }}" method="get">
                        <div class="input-group">
                            <label for="address" class="sr-only">Endereço</label>
                            <input type="text" class="form-control" name="address" id="address" value="{{ $address or "" }}">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">
                                    Localizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr>

            <div class="row">
                @foreach ($results as $result)
                <div class="col-4">
                    <div class="card" style="margin-bottom: 10px">
                        <div class="card-body">
                            <h4 class="card-title">{{ $result->street_name }}</h4>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $result->provider }}</h6>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                Relevância: {{ $result->relevance }}
                                <a href="#info-{{ $result->id }}" class="card-link" style="float: right" data-toggle="collapse" aria-expanded="false" aria-controls="info-{{ $result->id }}">
                                    Mais
                                </a>
                            </li>
                        </ul>
                        <div class="collapse" id="info-{{ $result->id }}">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Número: {{ $result->street_number }}</li>
                                <li class="list-group-item">Localidade: {{ $result->locality }}</li>
                                <li class="list-group-item">Sub-localidade: {{ $result->sub_locality }}</li>
                                <li class="list-group-item">País: ({{ $result->country_code }}) {{ $result->country_name }}</li>
                                <li class="list-group-item">Latitude: {{ $result->latitude }}</li>
                                <li class="list-group-item">Longitude: {{ $result->longitude }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </body>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $('.collapse').collapse({toggle: false});
    </script>
</html>