	<div class="simple-wrapper">
			<div class="ticket-show-related">
				<h4>Artigos Relacionados</h4>
				<ul>		
						@foreach($results as $result)
							<a href="{{ url('solution/show/' . $result->id) }}" target="_blank">
							<li>
								<img src="{{ URL::asset('img/right.png') }}">
								{{ $result->title }}
							</li>
							</a>
						@endforeach
				</ul>
			</div>

	</div>