<?php

namespace Kordy\Ticketit\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\BlinkHelper;
use App\Http\Models\Notification;
use App\Http\Models\TicketFile;
use App\Http\Models\AgentRate;
use App\Http\Models\Solution;
use Kordy\Ticketit\Models;
use Kordy\Ticketit\Models\Agent;
use Kordy\Ticketit\Models\Category;
use Kordy\Ticketit\Models\Setting;
use Kordy\Ticketit\Models\Ticket;
use Kordy\Ticketit\Models\Status;
use Kordy\Ticketit\Requests\PrepareTicketAgentRateRequest;
use Kordy\Ticketit\Requests\PrepareTicketStoreRequest;
use Kordy\Ticketit\Requests\PrepareTicketUpdateRequest;
use Kordy\Ticketit\Requests\TicketCreateFormRequest;
use Kordy\Ticketit\Requests\TicketFilterRequest;
use Kordy\Ticketit\Requests\TicketBlinkRequest;
use Kordy\Ticketit\Requests\TicketUsersFromCompanyRequest;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Engines\EloquentEngine;

class TicketsController extends Controller
{
    protected $tickets;
    protected $agent;

    public function __construct(Ticket $tickets, Agent $agent)
    {
        $this->middleware('Kordy\Ticketit\Middleware\ResAccessMiddleware', ['only' => ['show']]);
        $this->middleware('Kordy\Ticketit\Middleware\IsAgentMiddleware', ['only' => ['edit', 'update']]);
        $this->middleware('Kordy\Ticketit\Middleware\IsAdminMiddleware', ['only' => ['destroy']]);

        $this->tickets = $tickets;
        $this->agent = $agent;
    }

    /**
     * Display a listing of active tickets related to user.
     *
     * @return Response
     */
    public function index(TicketFilterRequest $request)
    {
        $statuses = Status::all();
        $categories = Category::all();

        $user = $this->agent->find(auth()->user()->id);

        if($user->isAdmin() || $user->isAgent()) {
            $companies = \App\Http\Models\Company::all();
            $company = $request->input('company');
        } else {
            $companies = null;
            $company = null;
        }

        if($user->isAdmin()) {
            $agents = \App\User::where('ticketit_agent', 1)->get();
            $agent = $request->input('agent');
        } else {
            $agents = null;
            $agent = null;
        }

        /**
         * Filter's Input
         */
        $category = $request->input('category');
        $status = $request->input('status');
        $order = $request->input('order');
        $query = $request->input('q');


        if($order == 2)
            $order_query = 'created_at';
        else 
            $order_query = 'updated_at';

        if ($user->isAdmin()) {
            $data_tickets = Ticket::where('status_id', '!=', 0)
                ->where('agent_id', '!=', '0');
        } elseif($user->isAgent()) {
            $data_tickets = Ticket::where('agent_id', $user->id);
        } else {
            $data_tickets = Ticket::where(function($query) use ($user) {
                    $query->orWhere(['user_id' => $user->id])
                        ->orWhereRaw("FIND_IN_SET('" . $user->id ."',conversation_id_list)");
            });
        }

        $data_tickets = $data_tickets->orderBy($order_query, 'desc');


        /**
         * Filter Queries
         *
         */

        if($query != null){
            //$data_tickets->search($query);
        }

        if($category != null) {
            $data_tickets->where('category_id', $category);
        }

        if($status != null) {
            $data_tickets->where('status_id', $status);
        } else {
            $data_tickets = $data_tickets->where('status_id', '!=', '3');
        }

        if($company != null) {
            $data_tickets->where('company_id', $company);
        }

        if($agent != null) {
            $data_tickets->where('agent_id', $agent);
        }


        $data_tickets = $data_tickets->paginate(6);
        $data_tickets->appends(['status' => $status]);
        $data_tickets->appends(['category' => $category]);

        if($order != null)
            $data_tickets->appends(['order' => $order]);

        return view('ticketit::index', compact('data_tickets'))
            ->with('statuses', $statuses)
            ->with('categories', $categories)
            ->with('category_input', $category)
            ->with('status_input', $status)
            ->with('order_input', $order)
            ->with('companies', $companies)
            ->with('company_input', $company)
            ->with('agent_input', $agent)
            ->with('agents', $agents);
    }
    /**
     * Display a listing of active tickets related to user.
     *
     * @return Response
     */
    public function lobby(TicketFilterRequest $request)
    {
        $statuses = Status::all();
        $categories = Category::all();

        $user = $this->agent->find(auth()->user()->id);

        $companies = \App\Http\Models\Company::all();
        $company = $request->input('company');

        $data_tickets = Ticket::where('agent_id',0)
            ->where('status_id', '!=', '3');
        $data_tickets = $data_tickets->paginate(6);


        return view('ticketit::tickets.lobby', compact('data_tickets'))
            ->with('statuses', $statuses)
            ->with('categories', $categories)
            ->with('companies', $companies);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param $id for sub-category 
     *
     * @return Response
     */
    public function create(TicketCreateFormRequest $request, $id = 0)
    {
        $user = $this->agent->find(auth()->user()->id);
        $priorities = Models\Priority::lists('name', 'id');
        $sub_category = \App\Http\Models\Category::find($id);
        $companies = null;

        $company_input = $request->company_id;

        if($user->isAgent() || $user->isAdmin())
            $companies = \App\Http\Models\Company::all();

        return view('ticketit::tickets.create', compact('priorities'))
                ->with('sub_category', $sub_category)
                ->with('companies', $companies)
                ->with('company_input', $company_input);
    }

    /**
     * Store a newly created ticket and auto assign an agent for it.
     *
     * @param Request $request
     *
     * @return Response redirect to index
     */
    public function store(PrepareTicketStoreRequest $request)
    {
        $user = $this->agent->find(auth()->user()->id);
        $ticket = new Ticket();

        $ticket->subject = $request->subject;

        if($user->isAgent() || $user->isAdmin())
            $ticket->setPurifiedContentByAgent($request->get('content') . $request->obs);
        else
            $ticket->setPurifiedContent($request->get('content') . $request->obs);

        $ticket->priority_id = $request->priority_id;
        $ticket->category_id = $request->category_id;
        $ticket->sub_category_id = $request->sub_category_id;
        $ticket->status_id = Setting::grab('default_status_id');
        $ticket->user_id = $user->id;
        $ticket->agent_id = 0;
        $ticket->company_id = $user->company_id;


        if($user->isAgent() || $user->isAdmin()) {
            if(isset($request->company_id) ) {
                $ticket->company_id = $request->company_id;

                if(isset($request->user_id)) {
                    $ticket->conversation_id_list = $request->user_id;

                    $action = "tickets/" . $ticket->id;
                    $image = "user/" . \Auth::user()->avatar;
                    $message =  "<b>" . \Auth::user()->name . "</b> criou o chamado #" . $ticket->subject . " para você.";

                    Notification::store($request->user_id, $message, $action, $image);
                } else {
                    $company = Company::where('id', $request->company_id)->get(['responsible']);
                    $ticket->conversation_id_list = $company->responsible;
                }

            }

            //if(isset($request->user_id))
            //    $ticket->user_id = $request->user_id;

        }

        if ($request->hasFile('file')) {
            $user = auth()->user();
            $destinationPath = "attachment/";
            $file = $request->file('file');
            $filename = $user->id * 100 + 32671231810 . $ticket->id . time();

            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $final_location = $destinationPath;
            $request->file('file')
                ->move($final_location, $filename.'.'. strtolower($request->file('file')
                ->getClientOriginalExtension()));
            
            $prepare_store_ticket_file = true;
        } else {
            $prepare_store_ticket_file = false;
        }

        $ticket->save();

        if($prepare_store_ticket_file) {
            $ticket_file = new TicketFile();
            $ticket_file->ticket_id = $ticket->id;
            $ticket_file->extension = strtolower($file->getClientOriginalExtension());
            $ticket_file->name = $filename;

            $ticket_file->save();
            $ticket->files = $ticket_file->id;
            $ticket->save();
        }

        $agents = \App\User::where('ticketit_agent', '1')->get();
        BlinkHelper::massPushBlink($agents, 'lobby');

        session()->flash('status', trans('ticketit::lang.the-ticket-has-been-created'));
        session()->flash('new_id', $ticket->id);

        return redirect()->action('\Kordy\Ticketit\Controllers\TicketsController@index');
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
        $user = $this->agent->find(auth()->user()->id);
        $ticket = $this->tickets->find($id);
        $status_lists = Models\Status::lists('name', 'id');
        $priority_lists = Models\Priority::lists('name', 'id');
        $category_lists = Models\Category::lists('name', 'id');
        $city = \App\Http\Models\City::find($ticket->user->company->city_id);
        $state = \App\Http\Models\State::find($ticket->user->company->state_id);
        $complete = false;
        $recent_tickets = array();

        if($user->isAgent())
            $recent_tickets = Ticket::where('user_id', $ticket->user_id)->where('id', '!=', $ticket->id)->orderBy('updated_at', 'desc')->limit(5)->get();

        $close_perm = $this->permToClose($id);
        $reopen_perm = $this->permToReopen($id);


        //Set automático de Agente caso o Chamado não tenha um.
        if($ticket->agent_id == 0 && $user->isAgent()) {
            $ticket->agent_id = $user->id;
            $ticket->save();

            $agents = \App\User::where('ticketit_agent', '1')->get();
            BlinkHelper::massPushBlink($agents, 'lobby');
            BlinkHelper::pushBlink($user->id, 'tickets');
        }

        $comments = $ticket->comments()->paginate(Setting::grab('paginate_items'));

        $cat_agents = Models\Category::find($ticket->category_id)->agents()->agentsLists();
        if ($comments->count() == 0) {
            $agent_lists = ['none' => 'None'];
        } else {
            $agent_lists = array();
        }

        if (is_array($cat_agents)) {
            $agent_lists += $cat_agents;
        } 


        $file = TicketFile::find($ticket->files);

        if ($ticket->completed_at != null) {
            $complete = true;
        }

        if($ticket->sub_category_id != 0)
            $search_query = $ticket->subcategory->getName();
        else 
            $search_query = " ";
        $results = Solution::where('status', 1)->search($search_query)->get();        


        return view('ticketit::tickets.show',
            compact('ticket', 'status_lists', 'priority_lists', 'category_lists', 'agent_lists', 'comments',
                'close_perm', 'reopen_perm'))
            ->with('city', $city)
            ->with('state', $state)
            ->with('file', $file)
            ->with('complete', $complete)
            ->with('results', $results)
            ->with('recent_tickets', $recent_tickets);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function quickShow($id)
    {
        $user = $this->agent->find(auth()->user()->id);
        $ticket = $this->tickets->find($id);
        $category_lists = Models\Category::lists('name', 'id');
        $complete = false;

        if($user->isAgent())
            $recent_tickets = Ticket::where('user_id', $ticket->user_id)->where('id', '!=', $ticket->id)->orderBy('updated_at', 'desc')->limit(5)->get();


        $comments = $ticket->comments()->paginate(Setting::grab('paginate_items'));

        $cat_agents = Models\Category::find($ticket->category_id)->agents()->agentsLists();
        if ($comments->count() == 0) {
            $agent_lists = ['none' => 'None'];
        } else {
            $agent_lists = array();
        }

        if (is_array($cat_agents)) {
            $agent_lists += $cat_agents;
        } 


        $file = TicketFile::find($ticket->files);

        if ($ticket->completed_at != null) {
            $complete = true;
        }

        if($ticket->sub_category_id != 0)
            $search_query = $ticket->subcategory->getName();
        else 
            $search_query = " ";
        $results = Solution::where('status', 1)->search($search_query)->get();        


        return view('ticketit::tickets.partials.quickshow',
            compact('ticket', 'status_lists', 'priority_lists', 'category_lists', 'agent_lists', 'comments',
                'close_perm', 'reopen_perm'))
            ->with('file', $file)
            ->with('complete', $complete)
            ->with('results', $results)
            ->with('recent_tickets', $recent_tickets);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update(PrepareTicketUpdateRequest $request, $id)
    {
        $user = $this->agent->find(auth()->user()->id);
        $ticket = $this->tickets->findOrFail($id);

        $ticket->subject = $request->subject;

        $ticket->setPurifiedContent($request->get('content'));

        $ticket->status_id = $request->status_id;
        $ticket->category_id = $request->category_id;
        $ticket->priority_id = $request->priority_id;

        //if ($request->input('agent_id') == 'auto') {
        //    $ticket->autoSelectAgent();
        if ($request->input('agent_id') == 'none' && $user->isAgent() ) {
            $ticket->agent_id = 0;
            $ticket->save();

            session()->flash('status', trans('Agente removido com sucesso!'));

            return redirect()->route(Setting::grab('main_route').'.index');
        } else {
            $ticket->agent_id = $request->input('agent_id');
        }

        $ticket->save();

        session()->flash('status', trans('ticketit::lang.the-ticket-has-been-modified'));

        return redirect()->route(Setting::grab('main_route').'.show', $id);
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
        $ticket = $this->tickets->findOrFail($id);
        $subject = $ticket->subject;
        $ticket->delete();

        session()->flash('status', trans('ticketit::lang.the-ticket-has-been-deleted', ['name' => $subject]));

        return redirect()->route(Setting::grab('main_route').'.index');
    }

    /**
     * Mark ticket as complete.
     *
     * @param int $id
     *
     * @return Response
     */
    public function complete($id)
    {
        if ($this->permToClose($id) == 'yes') {
            $ticket = $this->tickets->findOrFail($id);
            $ticket->completed_at = Carbon::now();

            if (Setting::grab('default_close_status_id')) {
                $ticket->status_id = Setting::grab('default_close_status_id');
            }

            $subject = $ticket->subject;
            $ticket->save();

            //Create Timeline Indicator
            $comment = new Models\Comment();

            $comment->content = $comment->getCloseToken();
            $comment->html = $comment->getCloseToken();
            $comment->ticket_id = $id;
            $comment->user_id = \Auth::user()->id;
            $comment->save();


            if(\Auth::user()->id != $ticket->user_id) {
                $action = "tickets/" . $ticket->id;
                $image = "user/" . \Auth::user()->avatar;
                $message =  "O chamado <b> #" . $ticket->subject . "</b> foi fechado.";

                Notification::store($ticket->user_id, $message, $action, $image);
            }

            session()->flash('status', trans('ticketit::lang.the-ticket-has-been-completed', ['name' => $subject]));

            return redirect()->route(Setting::grab('main_route').'.show', $id);
        }

        return redirect()->route(Setting::grab('main_route').'.show', $id)
            ->with('warning', trans('ticketit::lang.you-are-not-permitted-to-do-this'));
    }

    /**
     * Reopen ticket from complete status.
     *
     * @param int $id
     *
     * @return Response
     */
    public function reopen($id)
    {
        if ($this->permToReopen($id) == 'yes') {
            $ticket = $this->tickets->findOrFail($id);
            $ticket->completed_at = null;

            if (Setting::grab('default_reopen_status_id')) {
                $ticket->status_id = Setting::grab('default_reopen_status_id');
            }

            $subject = $ticket->subject;
            $ticket->save();

            //Create Timeline Indicator
            $comment = new Models\Comment();

            $comment->content = $comment->getOpenToken();
            $comment->html = $comment->getOpenToken();
            $comment->ticket_id = $id;
            $comment->user_id = \Auth::user()->id;
            $comment->save();

            if(\Auth::user()->id != $ticket->user_id) {
                $action = "tickets/" . $ticket->id;
                $image = "user/" . \Auth::user()->avatar;
                $message =  "O chamado <b> #" . $ticket->subject . "</b> foi aberto novamente.";

                Notification::store($ticket->user_id, $message, $action, $image);
            }
            session()->flash('status', trans('ticketit::lang.the-ticket-has-been-reopened', ['name' => $subject]));

            return redirect()->route(Setting::grab('main_route').'.show', $id);
        }

        return redirect()->route(Setting::grab('main_route').'.index')
            ->with('warning', trans('ticketit::lang.you-are-not-permitted-to-do-this'));
    }

    public function agentSelectList($category_id, $ticket_id)
    {
        $cat_agents = Models\Category::find($category_id)->agents()->agentsLists();
        if (is_array($cat_agents)) {
            $agents = ['auto' => 'Auto Select'] + $cat_agents;
        } else {
            $agents = ['auto' => 'Auto Select'];
        }

        $selected_Agent = $this->tickets->find($ticket_id)->agent->id;
        $select = '<select class="form-control" id="agent_id" name="agent_id">';
        foreach ($agents as $id => $name) {
            $selected = ($id == $selected_Agent) ? 'selected' : '';
            $select .= '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
        }
        $select .= '</select>';

        return $select;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function permToClose($id)
    {
        $close_ticket_perm = Setting::grab('close_ticket_perm');

        if ($this->agent->isAdmin() && $close_ticket_perm['admin'] == 'yes') {
            return 'yes';
        }
        if ($this->agent->isAgent() && $close_ticket_perm['agent'] == 'yes') {
            return 'yes';
        }
        if ($this->agent->isTicketOwner($id) && $close_ticket_perm['owner'] == 'yes') {
            return 'yes';
        }

        return 'no';
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function permToReopen($id)
    {
        $reopen_ticket_perm = Setting::grab('reopen_ticket_perm');
        if ($this->agent->isAdmin() && $reopen_ticket_perm['admin'] == 'yes') {
            return 'yes';
        } elseif ($this->agent->isAgent() && $reopen_ticket_perm['agent'] == 'yes') {
            return 'yes';
        } elseif ($this->agent->isTicketOwner($id) && $reopen_ticket_perm['owner'] == 'yes') {
            return 'yes';
        }

        return 'no';
    }

    /**
     * Calculate average closing period of days per category for number of months.
     *
     * @param int $period
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function monthlyPerfomance($period = 2)
    {
        $categories = Category::all();
        foreach ($categories as $cat) {
            $records['categories'][] = $cat->name;
        }

        for ($m = $period; $m >= 0; $m--) {
            $from = Carbon::now();
            $from->day = 1;
            $from->subMonth($m);
            $to = Carbon::now();
            $to->day = 1;
            $to->subMonth($m);
            $to->endOfMonth();
            $records['interval'][$from->format('F Y')] = [];
            foreach ($categories as $cat) {
                $records['interval'][$from->format('F Y')][] = round($this->intervalPerformance($from, $to, $cat->id), 1);
            }
        }

        return $records;
    }

    /**
     * Calculate the date length it took to solve a ticket.
     *
     * @param Ticket $ticket
     *
     * @return int|false
     */
    public function ticketPerformance($ticket)
    {
        if ($ticket->completed_at == null) {
            return false;
        }

        $created = new Carbon($ticket->created_at);
        $completed = new Carbon($ticket->completed_at);
        $length = $created->diff($completed)->days;

        return $length;
    }

    /**
     * Calculate the average date length it took to solve tickets within date period.
     *
     * @param $from
     * @param $to
     *
     * @return int
     */
    public function intervalPerformance($from, $to, $cat_id = false)
    {
        if ($cat_id) {
            $tickets = Ticket::where('category_id', $cat_id)->whereBetween('completed_at', [$from, $to])->get();
        } else {
            $tickets = Ticket::whereBetween('completed_at', [$from, $to])->get();
        }

        if (empty($tickets->first())) {
            return false;
        }

        $performance_count = 0;
        $counter = 0;
        foreach ($tickets as $ticket) {
            $performance_count += $this->ticketPerformance($ticket);
            $counter++;
        }
        $performance_average = $performance_count / $counter;

        return $performance_average;
    }

    public function rate(PrepareTicketAgentRateRequest $request) {
        $ticket = Ticket::find($request->ticket_id);

        $ticket->rate_id = $request->rate_id;

        $ticket->save();
        
        return view('ticketit::tickets.partials.rate_success')
            ->with('rate_name', $ticket->rate->name);
    }

    public function getUsersFromCompany(TicketUsersFromCompanyRequest $request) {
        $users = \App\User::where('company_id', $request->company_id)->get();
        return $users;
    }

    public function blink(TicketBlinkRequest $request) {
        if($request->hash == 'lobby') {
            if(BlinkHelper::popBlink($request->user_id, 'lobby'))
                $data = array('blink' => true);
            else 
                $data = array('blink' => false);

            return $data;
        } else {
            if(BlinkHelper::popBlink($request->user_id, 'tickets'))
                $data = array('blink' => true);
            else 
                $data = array('blink' => false);

            return $data;
        }
    }
}
