<div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            @if( $ticket->subcategory != null)
            <h4 class="modal-title">Categoria Problema: {{ $ticket->subcategory->getName() }}  </h4>
            @else 
            <h4 class="modal-title">Categoria Problema: Categoria não identificada </h4>
            @endif
        </div>
        <div class="modal-body">
            <div class="wrapper">
                <div class="ticket-view-header">
                    <h2>{{ $ticket->subject . " #" . $ticket->id }}
                    </h2>
                    <img class="avatar-small" src="{{ URL::asset('img/user') . '/' . ($ticket->user->avatar == null ? 'null.png' : $ticket->user->avatar) }}">
                    <h4><b>{{ $ticket->user->name }}</b>, criado há {{ $ticket->getCreatedScalarDate() }}.</h4>
             
                </div>

                <div class="ticket-view-body">
                    {!! $ticket->html !!} 
                    @if($ticket->files != null)
                        <br>
                        <br>
                        <hr class="attachment-line"/>
                        <p class="attachment-title">{{ $ticket->countAttachment($ticket) }} Anexo(s)
                        <br>
                        @if($file != null)
                            {!! $file->getHtmlPortrait() !!}
                        @endif
                    @endif
                </div>

                @include('ticketit::tickets.partials.comments')
                {!! $comments->render() !!}
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        </div>
    </div>
</div>
