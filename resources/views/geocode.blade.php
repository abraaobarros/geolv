<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #000;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
            }

            .input-address {
                width: 300px;
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            
            <div class="content">

                <form action="{{ url('/') }}" method="get">
                    <label>
                        Endereço:
                        <input type="text" name="address" class="input-address"/>
                    </label>
                    <input type="submit" value="Localizar"/>
                </form>

                <ul style="text-align: left">
                    @foreach ($results as $result)
                        <li>
                            <b>Provedor: </b> {{ $result->getProvidedBy() }}<br/>
                            <b>Logradouro: </b> {{ $result->getStreetName() }}<br/>
                            <b>Numero: </b> {{ $result->getStreetNumber() }}<br/>
                            <b>Pais: </b> {{ $result->getCountry()->getName() }}<br/>
                            <b>Latitude: </b> {{ $result->getCoordinates()->getLatitude() }}<br/>
                            <b>Longitude: </b> {{ $result->getCoordinates()->getLongitude() }}
                            <hr>
                        </li>
                    @endforeach
                </ul>
                
            </div>
        </div>
    </body>
</html>
