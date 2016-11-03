	<div class="tickets-filter">
		<h3 class="tickets-filter-title">
			Filtrar Tickets
		</h3>

		<form action="" method="get">
			<div class="tickets-filter-box">
				<h4 class="tickets-filter-box-title">Busca</h4>
				<input type="text" class="form-control" placeholder="Procurar Chamados" name="q">
			</div>

	        @if($u->isAgent() || $u->isAdmin())
	            <div class="tickets-filter-box">
	                <h4 class="tickets-filter-box-title">Organização</h4>

	                <select class="form-control" name="company">
	                    <option value="">Mostrar Tudo</option>
	                    @foreach ($companies as $company)
	                        @if($company->id == $company_input)
	                        <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
	                        @else
	                        <option value="{{ $company->id }}">{{ $company->name }}</option>
	                        @endif
	                    @endforeach
	                </select>
	            </div>
	        @endif

	        @if($u->isAdmin())
	            <div class="tickets-filter-box">
	                <h4 class="tickets-filter-box-title">Agente</h4>

	                <select class="form-control" name="agent">
	                    <option value="">Mostrar Tudo</option>
	                    @foreach ($agents as $agent)
	                        @if($agent->id == $agent_input)
	                        <option value="{{ $agent->id }}" selected>{{ $agent->name }}</option>
	                        @else
	                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
	                        @endif
	                    @endforeach
	                </select>
	            </div>
	        @endif

			<div class="tickets-filter-box">
				<h4 class="tickets-filter-box-title">Status</h4>

				<select class="form-control" name="status">
					<option value="">Em Aberto/Pendentes</option>
					@foreach ($statuses as $value => $status)
						@if($status->id == $status_input)
						<option value="{{ $status->id }}" selected>{{ $status->name }}</option>
						@else
						<option value="{{ $status->id }}">{{ $status->name }}</option>
						@endif
					@endforeach
				</select>
			</div>

			<div class="tickets-filter-box">
				<h4 class="tickets-filter-box-title">Categoria</h4>
				<select class="form-control" name="category">
					<option value="">Mostrar Tudo</option>
					@foreach ($categories as $value => $category)
						@if($category->id == $category_input)
						<option value="{{ $category->id }}" selected>{{ $category->name }}</option>
						@else
						<option value="{{ $category->id }}">{{ $category->name }}</option>
						@endif
					@endforeach
				</select>
			</div>

			<div class="tickets-filter-box">
				<h4 class="tickets-filter-box-title">Ordenar por</h4>
				<select class="form-control" name="order">
					@if($order_input == 2)
						<option value="1">Data de Atualização</option>
						<option value="2" selected>Data de Criação</option>
					@else
						<option value="1" selected>Data de Atualização	</option>
						<option value="2">Data de Criação</option>
					@endif
				</select>
			</div>
			<button type="submit" class="btn btn-primary btn-block">Filtrar</button>
		</form>
	</div>

	<div class="tickets-content-header">
	    <h3 class="tickets-title">Meus Chamados</h3>
    </div>
    <div id="ticket-list-wrapper">
		@include('pagination.tickets', ['paginator' => $data_tickets])
		<div class="tickets-list"> 
			@if($u->isAdmin()) 
				<div class="tickets-user-list">
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
						@if($ticket->status_id == $status_input || $status_input == 0)
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
		                    			style="border-left: 6px solid {{ $ticket->status->color }}; height: 100px;">
							<div class="ticket-status" style="border-color: {{ $ticket->status->color }}; background-color: {{ $ticket->status->color }};">
								{{ $ticket->status->name }}
								<div class="ticket-timer">
		                    		<h4 class="ticket-timer-user">{{ $ticket->created_at->diffForHumans() }}</h4>
		                    	</div>
							</div>
	                        <div class="tickets-info-box">
	                        	<h4>{{ $ticket->company->name }}</h4>
	                        	<h4><b>Agente:</b> {{ isset($ticket->agent) ? $ticket->agent->name : ""}}</h4>
	                        	<h4><b>Criado em: </b>{{ $ticket->getCreatedAt() }}</h4>
	                        </div>
							<img src="{{ URL::asset('img/category/' . $ticket->category->avatar) }}" class="tickets-avatar"> 
							<h5 class="ticket-title">#{{ $ticket->id }} <a class="ticket-action" href="{{ url('tickets/' . $ticket->id) }}">{{ $ticket->subject }}</a></h5>
							<h5 style="color: #000;">{{ $ticket->category->name }} | Última alteração: {{ $ticket->getScalarDate() }} atrás.</h5> 

							</div>
	                        {{--*/ $count++ /*--}}
						@endif
					@endforeach

	                @include('ticketit::tickets.partials.empty_box_loop')
				</div>
			@elseif($u->isAgent())
	            <div class="tickets-user-list">
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
						@if($ticket->status_id == $status_input || $status_input == 0)
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
		                    			style="border-left: 6px solid {{ $ticket->status->color }}; height: 100px;">
							<div class="ticket-status" style="border-color: {{ $ticket->status->color }}; background-color: {{ $ticket->status->color }};">
								{{ $ticket->status->name }}
								<div class="ticket-timer">
		                    		<h4 class="ticket-timer-user">{{ $ticket->created_at->diffForHumans() }}</h4>
		                    	</div>
							</div>
	                        <div class="tickets-info-box">
	                        	<h4><b> {{ $ticket->company->name }}</b></h4>
	                        	<h4>{{ isset($ticket->agent) ? $ticket->agent->name : "<< Aberto >>"}}</h4>
	                        	<!--<h4><b>Criado em: </b>{{ $ticket->getCreatedAt() }}</h4>-->
	                        </div>
							<img src="{{ URL::asset('img/category/' . $ticket->category->avatar) }}" class="tickets-avatar"> 
							<h5 class="ticket-title">#{{ $ticket->id }} <a class="ticket-action" href="{{ url('tickets/' . $ticket->id) }}">{{ $ticket->subject }}</a></h5>
							<h5 style="color: #000;">{{ $ticket->category->name }} | Última alteração: {{ $ticket->getScalarDate() }} atrás.</h5> 

							</div>
	                        {{--*/ $count++ /*--}}
						@endif
					@endforeach

	                @include('ticketit::tickets.partials.empty_box_loop')
	            </div>

			@else
				<div class="tickets-user-list">
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
	                    			style="border-left: 6px solid {{ $ticket->status->color }};">
						<div class="ticket-status" style="border-color: {{ $ticket->status->color }}; background-color: {{ $ticket->status->color }};">
							{{ $ticket->status->name }}
							<div class="ticket-timer">
	                    		<h4 class="ticket-timer-user">{{ $ticket->created_at->diffForHumans() }}</h4>
	                    	</div>
						</div>
						<img src="{{ URL::asset('img/category/' . $ticket->category->avatar) }}" class="tickets-avatar"> 
						<h5 class="ticket-title">#{{ $ticket->id }} <a class="ticket-action" href="{{ url('tickets/' . $ticket->id) }}">{{ $ticket->subject }}</a></h5>
						<h5 style="color: #000;">{{ $ticket->category->name }} | Última alteração: {{ $ticket->getScalarDate() }} atrás.</h5> 
						</div>
	                    {{--*/ $count++ /*--}}
					@endforeach

	                @include('ticketit::tickets.partials.empty_box_loop')
				</div>
			@endif
		</div>
	</div>
		<div class="clear"></div>