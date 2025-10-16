<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-700">
            🧑‍💻 Developer Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-4 text-indigo-600">
                    🎟 Tickets Assigned to You
                </h3>

                @if ($tickets->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $ticket->title ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $ticket->user->name ?? 'N/A' }}
                                        </td>

                                        <!-- Status Dropdown -->
                                        <td class="px-6 py-4">
                                            <form action="{{ route('developer.ticket.status', $ticket->id) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <div class="relative w-44">
                                                    <select name="status" onchange="this.form.submit()"
                                                        class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-3 pr-10 py-2.5 text-sm font-semibold shadow-sm transition-all duration-200
                                                            focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 cursor-pointer hover:bg-gray-100 appearance-none
                                                            {{ $ticket->status == 'Open' ? 'bg-green-100 text-green-800 border-green-200' : '' }}
                                                            {{ $ticket->status == 'In Progress' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                                                            {{ $ticket->status == 'Resolved' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' }}"
                                                        style="-webkit-appearance:none; -moz-appearance:none; appearance:none;">
                                                        <option value="Open"
                                                            {{ $ticket->status == 'Open' ? 'selected' : '' }}>Open
                                                        </option>
                                                        <option value="In Progress"
                                                            {{ $ticket->status == 'In Progress' ? 'selected' : '' }}>In
                                                            Progress</option>
                                                        <option value="Resolved"
                                                            {{ $ticket->status == 'Resolved' ? 'selected' : '' }}>
                                                            Resolved</option>
                                                    </select>

                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                                        <svg class="h-4 w-4 text-gray-500"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $ticket->product->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $ticket->created_at->diffForHumans() }}</td>

                                        <td class="px-6 py-4 text-sm text-gray-700 flex gap-2">
                                            <!-- View Details -->
                                            <button type="button"
                                                class="bg-indigo-500 text-white px-3 py-1 rounded-md hover:bg-indigo-600"
                                                onclick="openModal({{ $ticket->id }})">
                                                View
                                            </button>

                                            <!-- Comment -->
                                            <button type="button"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md"
                                                onclick="openCommentModal({{ $ticket->id }})">
                                                Comment
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- View Details Modal (per ticket) -->
                                    <div id="modal-{{ $ticket->id }}"
                                        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
                                        <div class="bg-white w-11/12 md:w-2/3 rounded-2xl shadow-lg p-6 relative">
                                            <button onclick="closeModal({{ $ticket->id }})"
                                                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✖</button>

                                            <h3 class="text-2xl font-semibold text-indigo-600 mb-4">
                                                {{ $ticket->title }}</h3>

                                            <p class="text-gray-700 mb-2"><strong>Product:</strong>
                                                {{ $ticket->product->name ?? 'N/A' }}</p>
                                            <p class="text-gray-700 mb-4"><strong>Description:</strong>
                                                {{ $ticket->description ?? 'No details provided' }}</p>

                                            @if ($ticket->media && $ticket->media->count())
                                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                                    @foreach ($ticket->media as $file)
                                                        <div class="border rounded-lg overflow-hidden">
                                                            <img src="{{ asset('storage/' . $file->file_path) }}"
                                                                alt="Ticket Media"
                                                                class="w-full h-40 object-cover cursor-pointer"
                                                                onclick="openImage('{{ asset('storage/' . $file->file_path) }}')">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-500 italic">No media files attached.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-6">No tickets assigned to you yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div id="image-viewer" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex justify-center items-center">
        <img id="image-view" src="" class="max-h-[80%] max-w-[90%] rounded-lg shadow-lg">
        <button onclick="closeImage()" class="absolute top-4 right-6 text-white text-2xl">✖</button>
    </div>

    <!-- Comment / Conversation Modal (single modal reused for all tickets) -->
    <div id="commentModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center px-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-lg overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 id="commentModalTitle" class="text-lg font-semibold text-gray-800">Conversation</h3>
                <button onclick="closeCommentModal()" class="text-gray-500 hover:text-gray-700">✖</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Left: ticket details -->
                <div class="md:col-span-1 p-4 border-r">
                    <p id="cm-ticket-title" class="font-semibold text-gray-800"></p>
                    <p id="cm-ticket-product" class="text-sm text-gray-600 mb-2"></p>
                    <div id="cm-ticket-desc" class="text-sm text-gray-700 mb-4"></div>
                    <div id="cm-media-preview" class="grid grid-cols-2 gap-2"></div>
                </div>

                <!-- Right: conversation -->
                <div class="md:col-span-2 flex flex-col">
                    <div id="conversationThread" class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50"></div>

                    <form id="conversationForm" class="p-4 border-t flex gap-2" onsubmit="return submitComment(event)">
                        @csrf
                        <input type="text" name="comment" id="conversationInput" placeholder="Type a message..."
                            class="flex-1 border rounded-full px-4 py-2 focus:ring focus:ring-indigo-200 focus:border-indigo-400"
                            required>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    // --- image modal functions ---
    function openModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }

    function openImage(src) {
        document.getElementById('image-view').src = src;
        document.getElementById('image-viewer').classList.remove('hidden');
    }

    function closeImage() {
        document.getElementById('image-viewer').classList.add('hidden');
    }

    // --- Comment / Conversation logic ---
    const conversationThread = document.getElementById('conversationThread');
    const conversationForm = document.getElementById('conversationForm');
    const conversationInput = document.getElementById('conversationInput');
    const commentModal = document.getElementById('commentModal');

    // store current ticketId in closure
    let CURRENT_TICKET_ID = null;

    // open comment modal for ticketId
    async function openCommentModal(ticketId) {
        CURRENT_TICKET_ID = ticketId;
        // Clear previous UI
        conversationThread.innerHTML =
            '<p class="text-gray-500 text-sm text-center mt-6">Loading conversation...</p>';
        document.getElementById('cm-media-preview').innerHTML = '';
        document.getElementById('cm-ticket-title').textContent = '';
        document.getElementById('cm-ticket-product').textContent = '';
        document.getElementById('cm-ticket-desc').textContent = '';

        // fetch ticket basic data + comments (controller route)
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch(`/developer/ticket/${ticketId}/comments`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
            });
            const payload = await res.json();

            // populate ticket info (if returned)
            if (payload.ticket) {
                document.getElementById('cm-ticket-title').textContent = payload.ticket.title || 'Untitled';
                document.getElementById('cm-ticket-product').textContent = payload.ticket.product_name ?
                    'Product: ' + payload.ticket.product_name : '';
                document.getElementById('cm-ticket-desc').textContent = payload.ticket.description || '';
                // preview media (if any)
                if (payload.ticket.media && payload.ticket.media.length) {
                    const preview = document.getElementById('cm-media-preview');
                    payload.ticket.media.forEach(m => {
                        const img = document.createElement('img');
                        img.src = m.url;
                        img.className = 'w-full h-20 object-cover rounded cursor-pointer';
                        img.onclick = () => openImage(m.url);
                        preview.appendChild(img);
                    });
                }
            }

            // render comments
            renderComments(payload.comments || []);
        } catch (err) {
            console.error('Failed to load comments', err);
            conversationThread.innerHTML =
                '<p class="text-red-500 text-sm text-center mt-6">Failed to load conversation.</p>';
        }

        commentModal.classList.remove('hidden');
        // focus input
        setTimeout(() => conversationInput.focus(), 250);

        if (window.commentRefreshInterval) clearInterval(window.commentRefreshInterval);
        window.commentRefreshInterval = setInterval(() => {
            if (CURRENT_TICKET_ID) {
                openCommentModal(CURRENT_TICKET_ID);
            }
        }, 5000); // refresh every 5 seconds

    }

    function closeCommentModal() {
        commentModal.classList.add('hidden');
        conversationThread.innerHTML = '';
        CURRENT_TICKET_ID = null;
        conversationInput.value = '';
        clearInterval(window.commentRefreshInterval);

    }

    function renderComments(comments = []) {
        conversationThread.innerHTML = '';
        if (!comments.length) {
            conversationThread.innerHTML = '<p class="text-gray-500 text-sm text-center mt-6">No messages yet.</p>';
            return;
        }

        comments.forEach(c => {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex ' + (c.is_me ? 'justify-end' : 'justify-start');

            const bubble = document.createElement('div');
            bubble.className = 'max-w-[80%] rounded-2xl px-4 py-2 text-sm ' + (c.is_me ?
                'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-800 rounded-bl-none');

            bubble.innerHTML =
                `<div>${escapeHtml(c.comment)}</div>
                                <div class="text-xs opacity-70 mt-1">${escapeHtml(c.user_name)} • ${escapeHtml(c.time)}</div>`;

            wrapper.appendChild(bubble);
            conversationThread.appendChild(wrapper);
        });

        conversationThread.scrollTop = conversationThread.scrollHeight;
    }

    // helper to escape
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    // submit comment via AJAX, append to thread on success
    // submit comment via AJAX, append to thread on success
    async function submitComment(e) {
        e.preventDefault();
        if (!CURRENT_TICKET_ID) return;

        const text = conversationInput.value.trim();
        if (!text) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const res = await fetch(`/developer/ticket/${CURRENT_TICKET_ID}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    comment: text
                })
            });

            const data = await res.json();
            if (res.ok && data.comment) {
                // ✅ append only the new comment (do NOT re-render all)
                const c = data.comment;

                const wrapper = document.createElement('div');
                wrapper.className = 'flex ' + (c.is_me ? 'justify-end' : 'justify-start');

                const bubble = document.createElement('div');
                bubble.className = 'max-w-[80%] rounded-2xl px-4 py-2 text-sm ' +
                    (c.is_me ? 'bg-indigo-600 text-white rounded-br-none' :
                        'bg-gray-100 text-gray-800 rounded-bl-none');

                bubble.innerHTML = `
                <div>${escapeHtml(c.comment)}</div>
                <div class="text-xs opacity-70 mt-1">${escapeHtml(c.user_name)} • ${escapeHtml(c.time)}</div>
            `;

                wrapper.appendChild(bubble);
                conversationThread.appendChild(wrapper);
                conversationThread.scrollTop = conversationThread.scrollHeight;

                conversationInput.value = '';
            } else {
                alert(data.message || 'Failed to post comment.');
            }
        } catch (err) {
            console.error('comment submit error', err);
            alert('Network error. Try again.');
        }
    }
</script>
