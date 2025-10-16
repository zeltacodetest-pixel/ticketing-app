<?php

namespace App\Http\Controllers\Developer;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketStatusUpdatedMail;

class DashboardController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'product', 'media'])
            ->where('assigned_to', Auth::id())
            ->latest()
            ->paginate(10);

        return view('dashboards.developer', compact('tickets'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:In Progress,Resolved',
        ]);

        $ticket = Ticket::where('assigned_to', Auth::id())->findOrFail($id);
        $ticket->status = $request->status;
        $ticket->save();

        // ✅ Find admin using Spatie role system
        $admin = User::role('admin')->first();  // <-- Correct
        $customer = User::find($ticket->user_id);

        if ($admin && $admin->email) {
            Mail::to($admin->email)->send(new TicketStatusUpdatedMail($ticket));
        }
        if ($customer && $customer->email) {
            Mail::to($customer->email)->send(new TicketStatusUpdatedMail($ticket));
        }

        return back()->with('success', '✅ Ticket status updated successfully.');
    }




    public function getComments($ticketId)
    {
        // ensure the developer is assigned to this ticket (security)
        $ticket = Ticket::with(['media'])->where('assigned_to', Auth::id())->findOrFail($ticketId);

        $comments = TicketComment::with('user')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'comment' => $c->comment,
                    'user_name' => $c->user->name ?? 'Unknown',
                    'is_me' => $c->user_id === Auth::id(),
                    'time' => Carbon::parse($c->created_at)->diffForHumans(),
                ];
            });

        // prepare ticket brief info for modal
        $ticketInfo = [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'product_name' => $ticket->product->name ?? null,
            'media' => $ticket->media->map(fn($m) => ['url' => asset('storage/' . $m->file_path)])->toArray(),
        ];

        return response()->json(['comments' => $comments, 'ticket' => $ticketInfo]);
    }

    public function storeComment(Request $request, $ticketId)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        // ensure the developer is assigned to this ticket
        $ticket = Ticket::where('assigned_to', Auth::id())->findOrFail($ticketId);

        $comment = TicketComment::create([
            'ticket_id' => $ticketId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        $payload = [
            'id' => $comment->id,
            'comment' => $comment->comment,
            'user_name' => $comment->user->name ?? Auth::user()->name,
            'time' => Carbon::parse($comment->created_at)->diffForHumans(),
        ];

        // Optionally return all comments or just the created comment
        return response()->json([
            'message' => 'Comment saved',
            'comment' => $payload
        ], 201);
    }
}
