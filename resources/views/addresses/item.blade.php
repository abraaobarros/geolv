<div class="card" style="margin-bottom: 10px">
    <div class="card-body">
        <h4 class="card-title">{{ $result->street_name }}</h4>
        <h6 class="card-subtitle mb-2 text-muted">{{ $result->provider }}</h6>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item list-group-item-default">
            Classificação: {{ $loop->iteration }}º
            <a href="#info-algorithm-{{ $result->id }}" class="card-link" style="float: right"
               data-toggle="collapse" aria-expanded="false" aria-controls="info-algorithm-{{ $result->id }}">
                <i class="fa fa-tasks"></i>
            </a>
        </li>
    </ul>
    <div class="collapse" id="info-algorithm-{{ $result->id }}">
        <ul class="list-group list-group-flush">
            @foreach ($result->algorithm as $key => $value)
                @if (filled($value))
                    <li class="list-group-item">
                        @if (starts_with($key, 'contains_') || starts_with($key, 'match_') )
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                       id="customCheck1" {{ ($value > 0? 'checked': '') }} disabled>
                                <label class="custom-control-label" for="customCheck1">
                                    <small>
                                        {{ trans("validation.attributes.$key") }}
                                    </small>
                                </label>
                            </div>
                        @else
                            <small>{{ trans("validation.attributes.$key") }}:</small>
                            <div class="progress">
                                <div class="progress-bar bg-{{ $value > 0 ? 'success' : 'danger' }}"
                                     style="width: {{ $value * 100 }}%">
                                    <small>{{ number_format($value * 100, 1) }}%</small>
                                </div>
                            </div>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item list-group-item-{{ $result->relevance > 0? 'default' : 'danger' }} text-center">
            <a href="#info-{{ $result->id }}" class="card-link mr-2" data-toggle="collapse" aria-expanded="false" aria-controls="info-{{ $result->id }}">
                <i class="fa fa-bars mr-1"></i>Abrir Resultado
            </a>
            <a href="{{ route('map', ['selected_id' => $result->id, 'search_id' => $result->search_id, 'providers' => $selectedProviders]) }}" class="card-link">
                <i class="fa fa-map mr-1"></i> Ver no mapa
            </a>
        </li>
    </ul>
    <div class="collapse" id="info-{{ $result->id }}">
        <ul class="list-group list-group-flush">
            @foreach ($result->fields as $key => $value)
                @if (filled($value))
                    <li class="list-group-item">
                        <span class="badge mr-1">{{ trans("validation.attributes.$key") }}:</span>
                        {{ $value }}
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>