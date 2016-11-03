<?php

namespace Kordy\Ticketit\Controllers;

use App\Helpers\BlinkHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kordy\Ticketit\Models;
use Kordy\Ticketit\Models\Agent;
use Kordy\Ticketit\Requests\PrepareCommentStoreRequest;
use App\Http\Models\Notification;

class CommentsController extends Controller
{


    public function __construct()
    {
        $this->middleware('Kordy\Ticketit\Middleware\IsAdminMiddleware', ['only' => ['edit', 'update', 'destroy']]);
        $this->middleware('Kordy\Ticketit\Middleware\ResAccessMiddleware', ['only' => 'store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PrepareCommentStoreRequest $request
     *
     * @return Response
     */
    public function store(PrepareCommentStoreRequest $request)
    {
        $user = Agent::find(auth()->user()->id);
        $comment = new Models\Comment();

        $comment->setPurifiedContent($request->get('content'));

        $comment->ticket_id = $request->get('ticket_id');
        $comment->user_id = \Auth::user()->id;
        $comment->save();

        $ticket = Models\Ticket::find($comment->ticket_id);

        if($user->isAgent() || $user->isAdmin()) {
            $ticket->status_id = 4;
            $ticket->agent_id = $user->id;

            $notification = new Notification();
            $notification->user_id = $ticket->user_id;
            $notification->content = "<b>" . \Auth::user()->name . "</b> respondeu o chamado #" . $ticket->subject . ".";
            $notification->action = "tickets/" . $ticket->id . "#" . $comment->id;
            $notification->status = 1;
            $notification->image = "user/" . \Auth::user()->avatar;
            $notification->save();
        } else {
            $notification = new Notification();
            $notification->user_id = $ticket->agent_id;
            $notification->content = "<b>" . \Auth::user()->name . "</b> respondeu o chamado #" . $ticket->subject . ".";
            $notification->action = "tickets/" . $ticket->id . "#" . $comment->id;
            $notification->status = 1;
            $notification->image = "user/" . \Auth::user()->avatar;
            $notification->save();
        }
        $ticket->updated_at = $comment->created_at;
        $ticket->save();

        BlinkHelper::pushBlink($ticket->user_id, 'tickets');


        return back()->with('status', trans('ticketit::lang.comment-has-been-added-ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
