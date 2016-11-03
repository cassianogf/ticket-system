    <div class="ticket-view-content">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
            <li class="breadcrumb-item active">{{ $ticket->subject }}</li>
        </ol>
        <div class="ticket-view-header">
            <h2>{{ $ticket->subject . " #" . $ticket->id }}
                @if($u->isAgent() || $u->isAdmin())
                <a href="" class="btn btn-link" 
                    title="Editar" 
                    data-toggle="modal" data-target="#ticket-edit-modal">
                        <i class="fa fa-pencil-square-o fa-2 pull-left"></i> 
                </a>
                @endif
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
        <div style="margin-top: 50px;">
            @include('ticketit::tickets.partials.comment_form')
        </div>
        @if($u->isAgent() || $u->isAdmin())
            @include('ticketit::tickets.partials.category_related')
        @endif
    </div>

    <div class="ticket-view-details">
        @if(!($u->isAgent() || $u->isAdmin()) )
            @if($complete)
                <div class="agent-rate">
                    <h4>Avalie Nosso Atendimento</h4>
                    <div id="agent-wrapper">
                        @if($ticket->rate_id == 0)
                            <h5>Lorem ipsum dolor lorem ipsum dolor lorem ipsum dolor</h5>
                            <div class="agent-rate-box" id="1">
                                <img src="{{ URL::asset('img/icons/smiling.png') }}" class="icon-smiley"></i>
                                <h5>Ótimo</h5>
                            </div>
                            <div class="agent-rate-box" id="2">
                                <img src="{{ URL::asset('img/icons/confused.png') }}" class="icon-smiley"></i>
                                <h5>Razoável</h5>
                            </div>
                            <div class="agent-rate-box" id="3">
                                <img src="{{ URL::asset('img/icons/sad.png') }}" class="icon-smiley"></i>
                                <h5>Ruim</h5>
                            </div>
                        @else
                            <h5>Você avaliou nosso atendimento como '{{ $ticket->rate->name }}'</h5>
                        @endif
                    </div>
                </div>
            @else
            
            @endif
        @endif

        <div class="ticket-view-status">
            <div class="ticket-view-status-color" style="
                background-color: {{ $ticket->status->color }};
            ">
            </div>
            <span class="pull-right" style="margin-top: 15px; margin-right: 10px;">
                @if(! $ticket->completed_at && $close_perm == 'yes')
                        {!! link_to_route($setting->grab('main_route').'.complete', trans('ticketit::lang.btn-mark-complete'), $ticket->id,
                                            ['class' => 'btn btn-success']) !!}
                @elseif($ticket->completed_at && $reopen_perm == 'yes')
                        {!! link_to_route($setting->grab('main_route').'.reopen', 'Reabrir', $ticket->id,
                                            ['class' => 'btn btn-success']) !!}
                @endif
                @if($u->isAdmin())
                    @if($setting->grab('delete_modal_type') != 'builtin')
                        {!! link_to_route(
                                        $setting->grab('main_route').'.destroy', trans('ticketit::lang.btn-delete'), $ticket->id,
                                        [
                                        'class' => 'btn btn-danger deleteit',
                                        'form' => "delete-ticket-$ticket->id",
                                        "node" => $ticket->subject
                                        ])
                        !!}
                    @elseif($setting->grab('delete_modal_type') != 'modal')
        {{-- // OR; Modal Window: 1/2 --}}
                        {!! CollectiveForm::open(array(
                                'route' => array($setting->grab('main_route').'.destroy', $ticket->id),
                                'method' => 'delete',
                                'style' => 'display:inline'
                           ))
                        !!}
                        <button type="button"
                                class="btn btn-danger"
                                data-toggle="modal"
                                data-target="#confirmDelete"
                                data-title="{!! trans('ticketit::lang.show-ticket-modal-delete-title', ['id' => $ticket->id]) !!}"
                                data-message="{!! trans('ticketit::lang.show-ticket-modal-delete-message', ['subject' => $ticket->subject]) !!}"
                         >
                          {{ trans('ticketit::lang.btn-delete') }}
                        </button>
                    @endif
                        {!! CollectiveForm::close() !!}
        {{-- // END Modal Window: 1/2 --}}
                @endif 
            </span>
            <h3>{{ $ticket->status->name}}</h3>
            <h4>Última Alteração {{ $ticket->updated_at->diffForHumans() }} </h4>
        </div>

        <div class="ticket-view-details-header">
            <img src="{{URL::asset('img/down_arrow.png')}}">
            <h3>Detalhes do Chamado</h3>
        </div>
        @if($u->isAdmin() || $u->isAgent())
        <h5>Atendente: {{ isset($ticket->agent) ? $ticket->agent->name : "" }}</h5>
        @endif
        <h5>Setor: {{ $ticket->category->name }}</h5>
        @if($ticket->subcategory != null)
            <h5>Sub-categoria: {{ $ticket->subcategory->getName() }}</h5>
        @endif
        <h5>Autor: {{ $ticket->user->name }}</h5>

        @if($u->isAdmin() || $u->isAgent())
        <h5>Prioridade: <span style="color: {{ $ticket->priority->color }}">{{ $ticket->priority->name }}</span></h5>
        @endif

        <div class="ticket-view-details-header">
            <img src="{{URL::asset('img/down_arrow.png')}}">
            <h3>Organização</h3>
        </div>
        <h5>Nome: {{ $ticket->company->name }}</h5>
        <h5>CNPJ: {{ $ticket->company->cnpj }}</h5>
        <h5>Endereço: {{ $ticket->company->address }}</h5>

        <h5>Cidade: {{ isset($city) ? $city->first()->name : "" }}</h5>
        <h5>Estado: {{ isset($state) ? $state->first()->name : "" }}</h5>
        <h5>CEP: {{ $ticket->company->zipcode }}</h5>
        @if($u->isAdmin() || $u->isAgent())
            <div class="ticket-view-details-header">
                <img src="{{URL::asset('img/down_arrow.png')}}">
                <h3>Histórico de Chamados</h3>
            </div>
            @foreach($recent_tickets as $recent_ticket) 
                <h5><a href="{{ url('tickets/quickshow/' . $recent_ticket->id) }}" data-remote="false" data-toggle="modal" data-target="#historyModal" class="modal-history-btn">{{ $recent_ticket->subject }}</a> #{{ $recent_ticket->id }}</h5>
            @endforeach
        @endif
        <br>
    </div>


    <!-- Modal for history -->
    <div id="historyModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Categoria Problema:  </h4>
                </div>
                <div class="modal-body">
                    <p>Carregando conteúdo...</p>
<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
<span class="sr-only">Loading...</span>
                </div>
                <div class="clear"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


        @if($u->isAgent() || $u->isAdmin())
            @include('ticketit::tickets.edit')
        @endif

    {{-- // OR; Modal Window: 2/2 --}}
        @if($u->isAdmin())
            @include('ticketit::tickets.partials.modal-delete-confirm')
        @endif
    {{-- // END Modal Window: 2/2 --}}
