<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Mail\TicketAssignedMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with all tickets.
     */
    public function index()
    {
        $tickets = Ticket::with(['user', 'product'])->latest()->paginate(10);
        $developers = User::role('Developer')->get(); // Requires Spatie roles

        return view('dashboards.admin', compact('tickets', 'developers'));
    }

    /**
     * Assign a ticket to a developer.
     */
    public function assignTicket(Request $request, $ticketId)
    {
        $request->validate([
            'developer_id' => 'nullable|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($ticketId);

        // Assign developer
        $ticket->assigned_to = $request->developer_id;

        // Update status if developer assigned
        if ($request->developer_id) {
            $ticket->status = 'Assigned';
        } else {
            $ticket->status = 'Open'; // if unassigned
        }

        $ticket->save();
        // Send to developer and customer
        $developer = User::find($ticket->assigned_to);
        $customer = User::find($ticket->user_id);

        if ($developer) {
            Mail::to($developer->email)->send(new TicketAssignedMail($ticket));
        }
        if ($customer) {
            Mail::to($customer->email)->send(new TicketAssignedMail($ticket));
        }
        return back()->with('success', '✅ Ticket assigned successfully.');
    }


    /**
     * Delete a ticket.
     */
    public function deleteTicket($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->delete();

        return back()->with('success', '🗑 Ticket deleted successfully.');
    }
}
