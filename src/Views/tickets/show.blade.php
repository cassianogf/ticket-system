@extends($master)
@section('page', trans('ticketit::lang.show-ticket-title') . trans('ticketit::lang.colon') . $ticket->subject)
@section('content')

    <div class="wrapper no-color margin-top">
    @include('ticketit::shared.header')
    </div>
    <div class="wrapper flex margin-top no-color">
        @include('ticketit::tickets.partials.ticket_body')
    </div>
@endsection

@section('footer')
<script>
    $(document).ready(function() {
        $( ".deleteit" ).click(function( event ) {
            event.preventDefault();
            if (confirm("{!! trans('ticketit::lang.show-ticket-js-delete') !!}" + $(this).attr("node") + " ?")) {
                var form = $(this).attr("form");
                $("#" + form).submit();
            }
        });

        $('#category_id').change(function(){
            var loadpage = "{!! route($setting->grab('main_route').'agentselectlist') !!}/" + $(this).val() + "/{{ $ticket->id }}";
            $('#agent_id').load(loadpage);
        });

        $('#confirmDelete').on('show.bs.modal', function (e) {
            $message = $(e.relatedTarget).attr('data-message');

            $(this).find('.modal-body p').text($message);
            $title = $(e.relatedTarget).attr('data-title');
            $(this).find('.modal-title').text($title);

            // Pass form reference to modal for submission on yes/ok
            var form = $(e.relatedTarget).closest('form');
            $(this).find('.modal-footer #confirm').data('form', form);
        });

        <!-- Form confirm (yes/ok) handler, submits form -->
        $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
            $(this).data('form').submit();
        });
    });
</script>

<script>
    $(function() {
        setTimeout(function() {
            if (location.hash) {
                /* we need to scroll to the top of the window first, because the browser will always jump to the anchor first before JavaScript is ready, thanks Stack Overflow: http://stackoverflow.com/a/3659116 */
                window.scrollTo(0, 0);
                target = location.hash.split('#');
                smoothScrollTo($('#'+target[1])); 
            }
        }, 1);

        // taken from: http://css-tricks.com/snippets/jquery/smooth-scrolling/
        $('a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                smoothScrollTo($(this.hash));
                return false;
            }
        });

        function smoothScrollTo(target) {
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');

            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 1000);
            }
        }

        $(".agent-rate-box").click(function() {
            $.post( "{{ URL::route('tickets.rate') }}", {
                ticket_id: {{ $ticket->id }},
                rate_id: this.id,
                _token: "{{ csrf_token() }}"
            })
              .done(function( data ) {
                $('#agent-wrapper').fadeOut(500, function() {
                    $(this).empty();
                    $(".agent-rate").after(data);
                });
            });
        });
    });


    $(document).ready(function(){
        $('[data-toggle="btn-tooltip"]').tooltip({'delay': { show: 100, hide: 10 }}); 
    });

    $(document).on("click", ".modal-history-btn", function (e) {
        var ticket_id = $(this).attr("href");
        $("#historyModal").empty();
        $("#historyModal").load(ticket_id);
    });    
</script>

@include('ticketit::tickets.partials.summernote')
@endsection
