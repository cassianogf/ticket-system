<div class="panel panel-default" style="margin-top:40px;">
    <div class="panel-body">
        @if (!$ticket->isComplete())
            {!! CollectiveForm::open(['method' => 'POST', 'route' => $setting->grab('main_route').'-comment.store', 'class' => 'form-horizontal', 'id' => 'reply-to-ticket']) !!}


                {!! CollectiveForm::hidden('ticket_id', $ticket->id ) !!}

                <fieldset>
                    <legend>Responder</legend>
                    <div class="form-group">
                        <div class="col-lg-12">
                            {!! CollectiveForm::textarea('content', null, ['class' => 'form-control summernote-editor', 'rows' => "3"]) !!}
                        </div>
                    </div>

                    <div class="text-right col-md-12">
                        {!! CollectiveForm::submit( trans('ticketit::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
                    </div>

                </fieldset>
            {!! CollectiveForm::close() !!}
        @else
            <div style="width: 100%; margin-top: 40px; margin-bottom: 40px; text-align: center;">
            <h4 style="margin: 0 auto;">É necessário abrir o chamado novamente para criar um comentário.</h4>
            </div>
        @endif
    </div>
</div>
