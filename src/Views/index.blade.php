@extends($master)


@section('content')
	<div class="wrapper no-color margin-top">
        @include('ticketit::shared.header')
    </div>
	<div class="wrapper border margin-top shadow"'>
        @include('ticketit::tickets.index')
    </div>
@endsection

@section('footer')
	<script>
		$("#new_ticket").one("mouseover", function() {
			$("#new_ticket").addClass('permahover');
		});

		
		//$(document).ready(function() {
		//	$.ajaxSetup({ cache: false }); // This part addresses an IE bug.  without it, IE will only load the first number and will never refresh
		//	setInterval(function() {
		//		$('.tickets-user-list').empty();
		//		$('.tickets-user-list').load("{{ url('tickets/') }} .tickets-user-list");
		//	}, 10000); // the "3000" 
		//});	
		
	</script>

    <script>
        (function ticketBlink() {
            $.ajax({
                url: "{{ URL::route('tickets.blink')}}", 
                type: 'post',
                data: { 
                    user_id: '{{ $u->id }}',
                    _token: '{{ csrf_token() }}',
                },
                success: function(data) {
                    if(data['blink']) {
                        $('#ticket-list-wrapper').empty();
                        $('#ticket-list-wrapper').load("{{ Request::fullUrl() }} #ticket-list-wrapper")
                    }
                    setTimeout(ticketBlink, 8000);
                },
                error: function(data) {
                    if(data.status == 401)
                        window.location.replace('{{ URL::route('solution') }}');
                    else if(data.status == 500) 
                        setTimeout(ticketBlink, 45000);


                    //alert('Request Status: ' + data.status + ' Status Text: ' + data.statusText + ' ' + data.responseText);
                    setTimeout(ticketBlink, 50000);
                }
            });
        })();
    </script>
@endsection
