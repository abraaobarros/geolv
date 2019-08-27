@if(!$file->done && !$file->canceled_at)
    <form action="{{ route('files.prioritize', $file->id) }}" method="post"
          class="d-inline-block">
        @csrf

        <div class="input-group input-group-sm">
            <input type="number" class="form-control" name="priority"
                   value="{{ $file->priority }}" min="0" step="1"/>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fa fa-arrows-v"></i>
                </button>
            </div>
        </div>
    </form>
@else
    <div class="input-group input-group-sm mb-3">
        <input type="number" class="form-control" name="priority"
               value="{{ $file->priority }}" min="0" step="1" readonly/>
        <div class="input-group-append">
            <button class="btn btn-outline-secondary disabled" disabled>
                <i class="fa fa-arrows-v"></i>
            </button>
        </div>
    </div>
@endif