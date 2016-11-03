@if(!$comments->isEmpty())
    @foreach($comments as $comment)
    	@if($comment->content == $comment->getOpenToken())
	        <div class="ticket-comment-open-header" id="{{ $comment->id }}">
	            <h4><b>{!! $comment->user->name !!}</b>, abriu o chamado {!! $comment->created_at->diffForHumans() !!}.</h4>
	        </div>
    	@elseif($comment->content == $comment->getCloseToken())
	        <div class="ticket-comment-close-header" id="{{ $comment->id }}">
	            <h4><b>{!! $comment->user->name !!}</b>, fechou o chamado {!! $comment->created_at->diffForHumans() !!}.</h4>
	        </div>
    	@else
	        <div class="ticket-comment-header" id="{{ $comment->id }}">
	            <img class="avatar-small" src="{{ URL::asset('img/user') . '/' . ($comment->user->avatar == null ? 'null.png' : $comment->user->avatar) }}">
	            <h4><b>{!! $comment->user->name !!}</b>, respondeu {!! $comment->created_at->diffForHumans() !!}.</h4>
	        </div>
	        <div class="ticket-comment-body">
	            {!! $comment->html !!}
	        </div>
    	@endif
    @endforeach
@endif