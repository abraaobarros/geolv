<div class="modal fade" tabindex="-1" role="dialog" id="{{ $id }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                @if(!empty($closable))
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                @endif
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(!empty($footer))
            <div class="modal-footer">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>