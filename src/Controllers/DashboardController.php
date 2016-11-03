<?php

namespace Kordy\Ticketit\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Kordy\Ticketit\Models\Agent;
use Kordy\Ticketit\Models\Category;
use Kordy\Ticketit\Models\Ticket;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function getBeloved($agents) {
        $i=0;
        
        $data = array();

        $rates = Ticket::where('rate_id', '!=', '0')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())->get();

        foreach($agents as $agent) {
            $count = 0;
            $j=0;

            foreach($rates as $rate_) {
                if($rate_->agent_id == $agent->id) {
                    if($rate_->rate_id == 1)
                        $count += 1;
                    elseif($rate_->rate_id == 2)
                        $count += 0.5;
                    else
                        $count -= 0.2;
                    $j++;
                }
            }
            if($j != 0)
                $count = $count * $j;

            $count /= $rates->count() + 1;
            $data[$i]['agent'] = $agent;
            $data[$i]['rate'] = $count;
            $i++;
        } 

        //Create index 'rate' for array multi sort
        foreach ($data as $row) {
                $rate[]  = $row['rate'];
        }        

        array_multisort($rate, SORT_DESC, $data);

        return $data;
    }

    public function getSpeedRacer($agents) {
        $i=0;
        
        $data = array();

        $tickets = Ticket::where('completed_at', '!=', null )
            ->where('created_at', '>=', Carbon::now()->startOfMonth())->get();

        foreach($agents as $agent) {
            $totalDuration = 0;
            $j=0;

            foreach($tickets as $ticket) {
                if($ticket->agent_id == $agent->id) {
                    $startTime = Carbon::parse($ticket->created_at);
                    $finishTime = Carbon::parse($ticket->completed_at);
                    $totalDuration = $totalDuration + $finishTime->diffinSeconds($startTime);
                    $j++;
                }
            }
            if($j != 0)
                $totalDuration *= $j;

            $totalDuration /= $tickets->count() + 1;
            $totalDuration /= 1000;

            $data[$i]['agent'] = $agent;
            $data[$i]['time'] = $totalDuration;
            $i++;
        } 

        //Create index 'rate' for array multi sort
        foreach ($data as $row) {
                $time[]  = $row['time'];
        }        

        array_multisort($time, SORT_ASC, $data);

        return $data;
    }

    public function getProdutivo($agents) {
        $i=0;
        
        $data = array();

        $tickets = Ticket::where('completed_at', '!=', null )
            ->where('created_at', '>=', Carbon::now()->startOfMonth())->get();

        foreach($agents as $agent) {
            $count = 0;
            $j=0;

            foreach($tickets as $ticket) {
                if($ticket->agent_id == $agent->id) {
                    $count++;
                    $j++;
                }
            }
            if($j != 0)
                $count *= $j;

            $count /= $tickets->count() + 1;
            $tickets_rateados = Ticket::where('rate_id', '!=', 0)
                ->where('agent_id', $agent->id)
                ->where('created_at', '>=', Carbon::now()->startOfMonth())->count();

            $data[$i]['agent'] = $agent;
            $data[$i]['score'] = ($count * 1.1 + $tickets_rateados * 1.2)/2 ;
            $i++;
        } 

        //Create index 'rate' for array multi sort
        foreach ($data as $row) {
                $score[]  = $row['score'];
        }        

        array_multisort($score, SORT_DESC, $data);

        return $data;
    }
    public function index($indicator_period = 2)
    {
        $tickets_count = Ticket::count();
        $open_tickets_count = Ticket::where('status_id', 1)->count();
        $pending_tickets_count = Ticket::where('status_id', 4)->count();
        $closed_tickets_count = Ticket::whereNotNull('completed_at')->count();

        // Per Category pagination
        $categories = Category::paginate(10, ['*'], 'cat_page');

        // Total tickets counter per category for google pie chart
        $categories_all = Category::all();
        $categories_share = [];
        foreach ($categories_all as $cat) {
            $categories_share[$cat->name] = $cat->tickets()->count();
        }

        // Total tickets counter per agent for google pie chart
        $agents_share_obj = Agent::agents()->with(['agentTotalTickets' => function ($query) {
            $query->addSelect(['id', 'agent_id']);
        }])->get();

        $agents_share = [];
        foreach ($agents_share_obj as $agent_share) {
            $agents_share[$agent_share->name] = $agent_share->agentTotalTickets->count();
        }

        // Per Agent
        $agents = Agent::agents(10);

        // Per User
        $users = Agent::users(10);

        // Per Category performance data
        $ticketController = new TicketsController(new Ticket(), new Agent());
        $monthly_performance = $ticketController->monthlyPerfomance($indicator_period);

        if (request()->has('cat_page')) {
            $active_tab = 'cat';
        } elseif (request()->has('agents_page')) {
            $active_tab = 'agents';
        } elseif (request()->has('users_page')) {
            $active_tab = 'users';
        } else {
            $active_tab = 'cat';
        }

        //Beloved
        $beloved_data = $this->getBeloved($agents_share_obj);

        //getSpeedRacer
        $speed_racer = $this->getSpeedRacer($agents_share_obj);

        //getProdutivo
        $produtivo = $this->getProdutivo($agents_share_obj);

        return view(
            'ticketit::admin.index',
            compact(
                'open_tickets_count',
                'pending_tickets_count',
                'closed_tickets_count',
                'tickets_count',
                'categories',
                'agents',
                'users',
                'monthly_performance',
                'categories_share',
                'agents_share',
                'active_tab',
                'beloved_data',
                'speed_racer',
                'produtivo'
            ));
    }
}
