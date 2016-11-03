<div class="panel panel-default">
    <div class="panel-body">
        <ul class="nav nav-pills">
            <li role="presentation" class="{!! $tools->fullUrlIs(action('\Kordy\Ticketit\Controllers\TicketsController@index')) ? "active" : "" !!}">
                <a href="{{ action('\Kordy\Ticketit\Controllers\TicketsController@index') }}">{{ trans('ticketit::lang.nav-active-tickets') }}
                    <span class="badge">
                        {{ Kordy\Ticketit\Models\Ticket::active()->agentUserTickets($u->id)->count() }}
                    </span>
                </a>
            </li>
        </ul>
    </div>
</div>
