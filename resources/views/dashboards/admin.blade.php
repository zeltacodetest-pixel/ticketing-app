<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-700 flex items-center gap-2"> 👑 Admin Dashboard </h2>
    </x-slot>

    <!-- Alpine root -->
    <div x-data="{ open: false, ticket: null }" class="py-10">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card with animation -->
            <div x-data x-init="$el.classList.add('opacity-0'); setTimeout(() => $el.classList.remove('opacity-0'), 100);"
                 class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl shadow-md p-8 mb-8 transition-all duration-700 opacity-100 transform translate-y-5">
                <h3 class="text-3xl font-bold mb-2">Welcome back, Admin!</h3>
                <p class="text-indigo-100 text-lg">Manage and assign support tickets efficiently.</p>
            </div>

            <!-- Ticket Table Card -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800">🎟️ All Tickets</h3>
                    <input type="text" placeholder="Search tickets..."
                        class="px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">ID</th>
                                <th class="px-6 py-3 text-left font-semibold">Title</th>
                                <th class="px-6 py-3 text-left font-semibold">Type</th>
                                <th class="px-6 py-3 text-left font-semibold">Product</th>
                                <th class="px-6 py-3 text-left font-semibold">Submitted By</th>
                                <th class="px-6 py-3 text-left font-semibold">Assigned To</th>
                                <th class="px-6 py-3 text-left font-semibold">Created</th>
                                <th class="px-6 py-3 text-center font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tickets as $ticket)
                                <tr class="hover:bg-indigo-50 transition">
                                    <td class="px-6 py-4 text-gray-700 font-medium">#{{ $ticket->id }}</td>
                                    <td class="px-6 py-4">{{ $ticket->title }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $ticket->type === 'sale' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ ucfirst($ticket->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $ticket->product->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $ticket->user->first_name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('admin.assign-ticket', $ticket->id) }}">
                                            @csrf
                                            <select name="developer_id" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">Unassigned</option>
                                                @foreach ($developers as $dev)
                                                    <option value="{{ $dev->id }}" {{ $ticket->assigned_to == $dev->id ? 'selected' : '' }}>
                                                        {{ $dev->first_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="ml-2 text-indigo-600 text-sm hover:underline">Assign</button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-sm">{{ $ticket->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- View Button -->
                                            <button @click="
                                                ticket = {
                                                    id: {{ $ticket->id }},
                                                    title: '{{ addslashes($ticket->title) }}',
                                                    description: '{{ addslashes($ticket->description ?? '') }}',
                                                    type: '{{ $ticket->type }}',
                                                    product: '{{ $ticket->product->name ?? '-' }}',
                                                    submitted_by: '{{ $ticket->user->first_name ?? 'Unknown' }}',
                                                    created_at: '{{ $ticket->created_at->diffForHumans() }}',
                                                    media: [
                                                        @foreach($ticket->media as $m)
                                                            { file_path: '{{ $m->file_path }}' },
                                                        @endforeach
                                                    ]
                                                };
                                                open = true;
                                            " class="text-blue-600 hover:text-blue-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Delete Button -->
                                            <form method="POST" action="{{ route('admin.delete-ticket', $ticket->id) }}" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t text-sm text-gray-600">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="open" x-cloak x-transition class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-xl shadow-lg w-2/3 max-h-[90vh] overflow-y-auto">
                <h2 class="text-2xl font-bold mb-4">Ticket Details</h2>

                <p><strong>ID:</strong> <span x-text="ticket.id"></span></p>
                <p><strong>Title:</strong> <span x-text="ticket.title"></span></p>
                <p><strong>Description:</strong> <span x-text="ticket.description"></span></p>
                <p><strong>Type:</strong> <span x-text="ticket.type"></span></p>
                <p><strong>Product:</strong> <span x-text="ticket.product"></span></p>
                <p><strong>Submitted By:</strong> <span x-text="ticket.submitted_by"></span></p>
                <p><strong>Created At:</strong> <span x-text="ticket.created_at"></span></p>

                <div>
                    <strong>Media:</strong>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <template x-for="m in ticket.media" :key="m.file_path">
                            <div>
                                <template x-if="m.file_path.endsWith('.jpg') || m.file_path.endsWith('.png') || m.file_path.endsWith('.jpeg')">
                                    <img :src="`/storage/${m.file_path}`" class="w-24 h-24 object-cover rounded" />
                                </template>
                                <template x-if="!(m.file_path.endsWith('.jpg') || m.file_path.endsWith('.png') || m.file_path.endsWith('.jpeg'))">
                                    <a :href="`/storage/${m.file_path}`" target="_blank" class="text-indigo-600 underline text-sm block" x-text="m.file_path"></a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <button @click="open = false" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">Close</button>
            </div>
        </div>

    </div>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout>
