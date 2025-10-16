<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-600 tracking-wide">
            🎫 Customer Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div x-data="ticketFlow(@js([
    'initialStep' => session()->has('ticket_submitted') ? 'success' : (old('type') ? 'form' : 'cards'),
    'initialType' => old('type'),
    'form' => [
        'subject' => old('subject', ''),
        'details' => old('details', ''),
        'productId' => old('product_id'),
    ],
]))" class="bg-white rounded-2xl shadow-xl p-10 relative overflow-hidden">
                <!-- Step 1: Card Selection -->
                <template x-if="step === 'cards'">
                    <div class="grid md:grid-cols-2 gap-8 relative z-10" x-init="$el.classList.add('animate-fade-in')">
                        <!-- Sales Card -->
                        <div @click="selectType('sale')"
                            class="cursor-pointer group p-8 rounded-2xl bg-white shadow-lg border border-gray-100 hover:scale-105 transition-transform duration-300 hover:shadow-xl opacity-0 animate-fade-in-up">
                            <div class="text-center">
                                <div
                                    class="bg-indigo-100 text-indigo-600 w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-4 group-hover:bg-indigo-200 transition-colors">
                                    💼
                                </div>
                                <h3 class="text-lg font-semibold">Sales Enquiry</h3>
                                <p class="text-gray-500 text-sm mt-2">Have questions about our products or pricing?</p>
                            </div>
                        </div>

                        <!-- Support Card -->
                        <div @click="selectType('support')"
                            class="cursor-pointer group p-8 rounded-2xl bg-white shadow-lg border border-gray-100 hover:scale-105 transition-transform duration-300 hover:shadow-xl opacity-0 animate-fade-in-up delay-200">
                            <div class="text-center">
                                <div
                                    class="bg-pink-100 text-pink-600 w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-4 group-hover:bg-pink-200 transition-colors">
                                    🛠
                                </div>
                                <h3 class="text-lg font-semibold">Customer Support</h3>
                                <p class="text-gray-500 text-sm mt-2">Need help with a product or service issue?</p>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Step 2: Form -->
                <template x-if="step === 'form'">
                    <div x-transition class="relative z-10">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold text-indigo-600"
                                x-text="selectedType === 'sale' ? 'Sales Enquiry' : 'Customer Support'"></h3>
                            <button @click="cancelForm" class="text-sm text-gray-500 hover:text-red-500 transition">✖
                                Cancel</button>
                        </div>

                        <div class="space-y-5">
                            @if ($errors->any())
                                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                    <p class="font-semibold">We couldn't submit your ticket:</p>
                                    <ul class="mt-2 list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <form method="POST" enctype="multipart/form-data"
                            action="{{ route('customer.tickets.store') }}" class="mt-6 space-y-5">
                            @csrf
                            <input type="hidden" name="type" :value="selectedType">
                            <div>
                                <label class="block text-sm font-medium mb-1">Subject</label>
                                <input type="text" name="subject" x-model="form.subject" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 placeholder-gray-400 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Product</label>
                                @if ($products->isEmpty())
                                    <p
                                        class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
                                        No products are available yet. Please contact the administrator.
                                    </p>
                                @else
                                    <select name="product_id" x-model="form.productId" required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition">
                                        <option value="" disabled x-bind:selected="!form.productId">Select a
                                            product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Details</label>
                                <textarea rows="4" name="details" x-model="form.details" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 placeholder-gray-400 transition"></textarea>
                            </div>
                            <div x-data="fileUploader()" class="mb-6">
                                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                                    Attachments
                                </label>

                                <!-- Drop zone -->
                                <div x-on:dragover.prevent="drag = true" x-on:dragleave.prevent="drag = false"
                                    x-on:drop.prevent="handleDrop($event)"
                                    :class="drag ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 bg-white'"
                                    class="relative flex flex-col items-center justify-center w-full h-40 p-6 border-2 border-dashed rounded-2xl transition-all cursor-pointer">
                                    <input type="file" name="attachments[]" id="attachments" multiple
                                        x-ref="fileInput" class="absolute inset-0 opacity-0 cursor-pointer"
                                        x-on:change="handleFiles($event.target.files)">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-indigo-400 mb-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 15a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4m0 0a4 4 0 0 0 4 4h2a4 4 0 0 0 4-4m-8 0V3" />
                                    </svg>
                                    <p class="text-gray-600 text-sm text-center">
                                        <span class="font-medium text-indigo-600 hover:text-indigo-700">Click to
                                            upload</span>
                                        or drag & drop files here
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, PDF, DOC up to 5MB each</p>
                                </div>

                                <!-- Preview -->
                                <template x-if="files.length > 0">
                                    <ul class="mt-4 space-y-2">
                                        <template x-for="(file, index) in files" :key="index">
                                            <li
                                                class="flex items-center justify-between p-3 bg-gray-50 border rounded-xl">
                                                <div class="flex items-center space-x-3">
                                                    <template x-if="file.type.startsWith('image/')">
                                                        <img :src="file.preview"
                                                            class="w-10 h-10 object-cover rounded-lg border"
                                                            alt="">
                                                    </template>
                                                    <template x-if="!file.type.startsWith('image/')">
                                                        <div
                                                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 font-semibold">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1.5" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </div>
                                                    </template>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800 truncate"
                                                            x-text="file.name"></p>
                                                        <p class="text-xs text-gray-500"
                                                            x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="text-gray-400 hover:text-red-600 transition"
                                                    @click="removeFile(index)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                            </div>

                            <button type="submit" @if ($products->isEmpty()) disabled @endif
                                class="w-full py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60 text-white font-semibold shadow-md transition transform hover:scale-105">
                                📩 Submit Ticket
                            </button>
                        </form>
                    </div>
                </template>

                <!-- Step 3: Success Message -->
                <template x-if="step === 'success'">
                    <div x-transition class="text-center relative z-10">
                        <div class="text-6xl mb-4">✅</div>
                        <h3 class="text-2xl font-bold text-green-600">
                            {{ session('ticket_submitted', 'Your ticket has been submitted!') }}</h3>
                        <p class="text-gray-600 mt-2">We’ll get back to you as soon as possible.</p>
                        <button @click="resetFlow"
                            class="mt-6 px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-md transition">
                            Back to Dashboard
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function ticketFlow(config = {}) {
        const form = config.form || {};

        return {
            step: config.initialStep || 'cards',
            selectedType: config.initialType || null,
            form: {
                subject: form.subject || '',
                details: form.details || '',
                productId: form.productId || '',
            },
            selectType(type) {
                this.selectedType = type;
                this.step = 'form';
            },
            cancelForm() {
                this.form = {
                    subject: '',
                    details: '',
                    productId: ''
                };
                this.step = 'cards';
                this.selectedType = null;
            },
            resetFlow() {
                window.location.href = '{{ route('customer.dashboard') }}';
            }
        }
    }

      function fileUploader() {
        return {
            drag: false,
            files: [],
            handleFiles(fileList) {
                for (const file of fileList) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        this.files.push({
                            name: file.name,
                            size: file.size,
                            type: file.type,
                            preview: e.target.result,
                            raw: file
                        });
                    };
                    reader.readAsDataURL(file);
                }
            },
            handleDrop(event) {
                this.drag = false;
                this.handleFiles(event.dataTransfer.files);
            },
            removeFile(index) {
                this.files.splice(index, 1);
                // also clear input if needed
                this.$refs.fileInput.value = '';
            }
        };
    }
</script>

<!-- Animations -->
<style>
    @keyframes fade-in-up {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.7s ease-out forwards;
    }

    .delay-200 {
        animation-delay: 0.2s;
    }
</style>
