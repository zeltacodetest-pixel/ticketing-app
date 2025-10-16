<?php

namespace App\Http\Controllers\Customer;

use App\Models\Ticket;
use App\Models\Product;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard with products for ticket submission.
     */
    public function __invoke(Request $request): View
    {
        $products = Product::orderBy('name')->get();

        return view('dashboards.customer', [
            'products' => $products,
        ]);
    }

    /**
     * Show all tickets for the logged-in customer.
     */
    public function showTickets()
    {
        $tickets = Ticket::with([
            'comments' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'comments.user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            },
        ])
            ->where('user_id', auth()->id())
            ->get();

        return view('dashboards.customer.showTicket', compact('tickets'));
    }



    public function tickets()
    {
        $tickets = Ticket::with([
            'comments.user' => function ($query) {
                $query->select('id', 'name');
            },
            'comments' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])
            ->where('user_id', auth()->id())
            ->get();

        return view('dashboards.customer.showTicket', compact('tickets'));
    }


    /**
     * Store a new message/comment for a ticket.
     */
   public function storeComment(Request $request, $ticketId)
{
    try {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($ticketId);

        $comment = $ticket->comments()->create([
            'comment' => $request->comment,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user:id,first_name,last_name'),
        ]);

    } catch (\Exception $e) {
        // 👇 this ensures you still get JSON instead of HTML error
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
