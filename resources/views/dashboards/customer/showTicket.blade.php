<x-app-layout>
    <div class="p-6 bg-white shadow rounded-lg">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">My Tickets</h2>

        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Title</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Created</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $ticket->title }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold 
                                @if ($ticket->status === 'Open') bg-green-100 text-green-700
                                @elseif($ticket->status === 'In Progress') bg-yellow-100 text-yellow-700
                                @elseif($ticket->status === 'Resolved') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <button onclick="openChat({{ $ticket->id }})"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                                Comments
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 🗨 Chat Modal -->
    <div id="chatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Ticket Conversation</h3>
                <button onclick="closeChat()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>

            <!-- Messages area -->
            <div id="conversationThread" class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50"></div>

            <!-- Send Message -->
            <form id="chatForm" method="POST" class="p-4 border-t flex space-x-2">
                @csrf
                <input type="hidden" name="ticket_id" id="ticket_id">
                <input type="text" name="message" placeholder="Type a message..."
                    class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-indigo-200" required>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Send</button>
            </form>
        </div>
    </div>

    <script>
const tickets = @json($tickets);

function openChat(ticketId) {
    const modal = document.getElementById('chatModal');
    const thread = document.getElementById('conversationThread');
    const form = document.getElementById('chatForm');

    const ticket = tickets.find(t => t.id === ticketId);

    document.getElementById('ticket_id').value = ticketId;
    form.setAttribute('data-ticket-id', ticketId);
    form.action = `/customer/tickets/${ticketId}/comment`; // ✅ correct route

    thread.innerHTML = '';
    if (!ticket.comments || ticket.comments.length === 0) {
        thread.innerHTML = `<p class="text-center text-gray-400 text-sm mt-6">No messages yet. Start the conversation.</p>`;
    } else {
        ticket.comments.forEach(comment => appendMessage(comment));
    }

    modal.classList.remove('hidden');
}

function closeChat() {
    document.getElementById('chatModal').classList.add('hidden');
}

// ✅ Append a message bubble (customer/admin)
function appendMessage(comment) {
    const thread = document.getElementById('conversationThread');
    const isMine = comment.user_id === {{ auth()->id() }};
    const senderName = comment.user
        ? `${comment.user.first_name ?? ''} ${comment.user.last_name ?? ''}`.trim()
        : 'Unknown';

    const wrapper = document.createElement('div');
    wrapper.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;

    const bubble = document.createElement('div');
    bubble.className = `
        max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-sm 
        ${isMine ? 'bg-green-500 text-white rounded-br-none' : 'bg-gray-100 text-gray-800 rounded-bl-none'}
    `;

    bubble.innerHTML = `
        <div class="font-semibold text-xs mb-1">${senderName}</div>
        <div>${escapeHtml(comment.comment)}</div>
        <div class="text-xs opacity-70 mt-1 text-right">${new Date(comment.created_at).toLocaleString()}</div>
    `;

    wrapper.appendChild(bubble);
    thread.appendChild(wrapper);
    thread.scrollTop = thread.scrollHeight;
}

// ✅ Escape HTML to avoid XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ✅ AJAX Submit (Send new comment)
document.getElementById('chatForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const ticketId = this.getAttribute('data-ticket-id');
    const input = this.querySelector('input[name="message"]');
    const message = input.value.trim();

    if (!message) return;

    try {
        const res = await fetch(`/customer/tickets/${ticketId}/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                ticket_id: ticketId,
                comment: message,
            }),
        });

        if (!res.ok) throw new Error('Bad response');

        const data = await res.json();
        if (data && data.comment) {
            appendMessage(data.comment);
            input.value = '';
        } else {
            alert('Failed to send message.');
        }
    } catch (err) {
        console.error(err);
        alert('Network or server error while sending message.');
    }
});


    </script>
</x-app-layout>
