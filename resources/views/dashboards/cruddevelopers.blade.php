{{-- Make sure your layout includes: <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
<style>[x-cloak]{display:none !important;}</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-700">🧑‍💻 Developer Management</h2>
    </x-slot>

    <div x-data="developerCrud()" x-cloak class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- ✅ Success Toast -->
            <template x-if="toast.show">
                <div x-transition class="fixed top-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50" x-cloak>
                    ✅ <span x-text="toast.message"></span>
                </div>
            </template>

            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl shadow-md p-8 mb-8">
                <h3 class="text-3xl font-bold mb-2">Manage Developers</h3>
                <p class="text-indigo-100 text-lg">Create, edit, and manage your development team members easily.</p>
            </div>

            <!-- Header -->
            <div class="bg-white shadow-md rounded-2xl p-6 mb-6 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-700">Developers</h3>
                <button type="button" @click="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">
                    + Add Developer
                </button>
            </div>

            <!-- Developer Table -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">ID</th>
                            <th class="px-6 py-3 text-left font-semibold">Name</th>
                            <th class="px-6 py-3 text-left font-semibold">Email</th>
                            <th class="px-6 py-3 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($developers as $dev)
                            <tr id="dev-row-{{ $dev->id }}" class="hover:bg-indigo-50 transition">
                                <td class="px-6 py-4 text-gray-700">#{{ $dev->id }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $dev->first_name }} {{ $dev->last_name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $dev->email }}</td>
                                <td class="px-6 py-4 text-center flex justify-center gap-2">
                                    {{-- ensure passing clean JSON (fix for edit modal) --}}
                                    <button type="button" 
                                        @click="openEditModal({ 
                                            id: {{ $dev->id }}, 
                                            first_name: '{{ $dev->first_name }}', 
                                            last_name: '{{ $dev->last_name }}', 
                                            email: '{{ $dev->email }}' 
                                        })" 
                                        class="text-blue-600 hover:text-blue-800">
                                        ✏️
                                    </button>

                                    <button type="button" 
                                        @click="confirmDelete('{{ route('admin.developers.destroy', $dev->id) }}')" 
                                        class="text-red-600 hover:text-red-800">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 🟢 Create / Edit Modal -->
        <div x-show="modal.open" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative">
                <button type="button" @click="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✖</button>
                <h3 class="text-xl font-bold text-indigo-600 mb-4" x-text="modal.isEdit ? '✏️ Edit Developer' : '➕ Add Developer'"></h3>

                <form @submit.prevent="saveDeveloper()">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">First Name</label>
                            <input type="text" x-model="form.first_name" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Last Name</label>
                            <input type="text" x-model="form.last_name" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <input type="email" x-model="form.email" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-6" x-show="!modal.isEdit">
                        <label class="text-sm font-medium text-gray-600">Password</label>
                        <input type="password" x-model="form.password" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 🔴 Delete Confirmation -->
        <div x-show="deleteModal.open" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Delete Developer?</h3>
                <p class="text-gray-500 mb-5">This action cannot be undone.</p>
                <div class="flex justify-center gap-3">
                    <button type="button" @click="deleteModal.open=false" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</button>
                    <button type="button" @click="deleteDeveloper()" class="px-4 py-2 bg-red-600 text-white rounded-lg">Delete</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('developerCrud', () => ({
        modal: { open: false, isEdit: false },
        deleteModal: { open: false, url: '' },
        form: { id: null, first_name: '', last_name: '', email: '', password: '' },
        toast: { show: false, message: '' },

        openCreateModal() {
            this.resetForm();
            this.modal.isEdit = false;
            this.modal.open = true;
        },

        openEditModal(dev) {
            this.form = { ...dev, password: '' };
            this.modal.isEdit = true;
            this.modal.open = true;
        },

        closeModal() {
            this.modal.open = false;
        },

        confirmDelete(url) {
            this.deleteModal.url = url;
            this.deleteModal.open = true;
        },

        async saveDeveloper() {
            const method = this.modal.isEdit ? 'PUT' : 'POST';
            const url = this.modal.isEdit ? `/admin/developers/${this.form.id}` : `/admin/developers`;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                // Try to read JSON if available
                const data = await res.json().catch(() => ({}));

                if (res.status === 200 || res.status === 201) {
                    this.showToast(data.message || 'Saved successfully!');
                    this.modal.open = false;
                    setTimeout(() => window.location.reload(), 700);
                } else {
                    console.error('Save error:', data);
                    this.showToast(data.message || 'Save failed.');
                }
            } catch (err) {
                console.error('Save error:', err);
                this.showToast('Network or server error.');
            }
        },

        async deleteDeveloper() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const res = await fetch(this.deleteModal.url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json().catch(() => ({}));

                if (res.status === 200 || res.status === 204) {
                    this.showToast(data.message || 'Deleted successfully!');
                    this.deleteModal.open = false;
                    setTimeout(() => window.location.reload(), 700);
                } else {
                    console.error('Delete error:', data);
                    this.showToast(data.message || 'Delete failed.');
                }
            } catch (err) {
                console.error('Delete error:', err);
                this.showToast('Network or server error.');
            }
        },

        resetForm() {
            this.form = { id: null, first_name: '', last_name: '', email: '', password: '' };
        },

        showToast(msg) {
            this.toast.message = msg;
            this.toast.show = true;
            setTimeout(() => (this.toast.show = false), 3000);
        }
    }));
});
</script>

