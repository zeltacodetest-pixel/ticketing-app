<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use App\Mail\TicketCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

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
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ]);

        // Create ticket
        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
            'title' => $validated['subject'],
            'type' => $validated['type'],
            'description' => $validated['details'],
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets', 'public');
                $type = $file->getClientMimeType();

                $ticket->media()->create([
                    'file_path' => $path,
                    'file_type' => $type,
                ]);
            }
        }

        // 🔹 Send email to admin(s)
        $admins = User::role('admin')->get(); // Spatie method
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new TicketCreatedMail($ticket));
        }

        return redirect()
            ->route('customer.tickets.show', ['ticket' => $ticket->id])
            ->with('ticket_submitted', 'Your ticket has been submitted!');
    }

    public function storeComment(Request $request, $ticketId)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        TicketComment::create([
            'ticket_id' => $ticketId,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }
}
