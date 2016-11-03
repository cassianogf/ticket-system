@extends($master)


@section('content')
	<div class="wrapper no-color margin-top">
        @include('ticketit::shared.header')
    </div>
	<div class="wrapper border margin-top shadow">
		<div class="tickets-content-header">
		    <h3 class="tickets-title">Chamados em Aberto</h3>
	    </div>
	    <div id="ticket-list-wrapper">
			@include('pagination.tickets', ['paginator' => $data_tickets])
	    	<div class="clear"></div>
			<div class="tickets-list"> 
				@if($u->isAgent() || $u->isAdmin())
		            <div class="tickets-user-list" style="margin: 0 auto; float: none; margin-bottom: 20px;">
						<div class="tickets-user-header">
							<div class="tickets-user-header-info">
								Informações do Chamado
							</div>
							<div class="tickets-user-header-status">
								Status
							</div>
						</div>
		                {{--*/ $count = 0 /*--}}
						@foreach ($data_tickets as $value => $ticket)
			                    <div class="tickets-user-list-box 
			                    			{{ $count % 2 != 0 ? 'even' : '' }} 
			                    			@if(Session::has('new_id'))
			                    				@if(session('new_id') == $ticket->id)
			                    					new" id="new_ticket"
			                    				@else
			                    					"
			                    				@endif
			                    			@else
			                    				"
			                    			@endif
			                    			style="border-left: 6px solid {{ $ticket->status->color }}; height: 85px;">
								<div class="ticket-status" style="border-color: {{ $ticket->status->color }}; background-color: {{ $ticket->status->color }}; height: 30px;">
									{{ $ticket->status->name }}
									<div class="ticket-timer">
			                    		<h4 class="ticket-timer-user">{{ $ticket->created_at->diffForHumans() }}</h4>
			                    	</div>
								</div>
		                        <div class="tickets-info-box">
		                        	<h4><b> {{ $ticket->company->name }}</b></h4>
		                        	<h4>{{ isset($ticket->agent) ? $ticket->agent->name : "<< Aberto >>"}}</h4>
		                        </div>
								<img src="{{ URL::asset('img/category/' . $ticket->category->avatar) }}" class="tickets-avatar"> 
								<h5 class="ticket-title">#{{ $ticket->id }} <a class="ticket-action" href="{{ url('tickets/' . $ticket->id) }}">{{ $ticket->subject }}</a></h5>
								<h5 style="color: #000;">{{ $ticket->category->name }}</h5> 

								</div>
		                        {{--*/ $count++ /*--}}
						@endforeach

		                @include('ticketit::tickets.partials.empty_box_loop')
		            </div>
				@endif
			</div>
		</div>
		<div class="clear"></div>
	</div>

@endsection

@section('footer')

    <script>
        (function lobbyBlink() {
            $.ajax({
                url: "{{ URL::route('tickets.blink')}}", 
                type: 'post',

                async: true,
                timeout: 50000,
                data: { 
                    user_id: '{{ $u->id }}',
                    hash: 'lobby',
                    _token: '{{ csrf_token() }}',
                },

                success: function(data) {
                    if(data['blink']) {
                        $('#ticket-list-wrapper').empty();
                        $('#ticket-list-wrapper').load("{{ Request::fullUrl() }} #ticket-list-wrapper")
                    }
                },
                complete: function(data) {

                    setTimeout(lobbyBlink, 8000);
                }
            });
        })();
    </script>

@endsection