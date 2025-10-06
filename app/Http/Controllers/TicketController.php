<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:support,sale'],
        ]);

        Ticket::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
            'title' => $validated['subject'],
            'type' => $validated['type'],
            'description' => $validated['details'],
        ]);

        return redirect()
            ->route('customer.dashboard')
            ->with('ticket_submitted', 'Your ticket has been submitted!');
    }
}
