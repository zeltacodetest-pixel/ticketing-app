<h2>{{ $ticket->title }}</h2>
<p>Status: {{ $ticket->status }}</p>
<p>Details: {{ $ticket->description }}</p>
<p>View Ticket: {{ url('/admin/tickets/'.$ticket->id) }}</p>
<p>Thank you!</p>