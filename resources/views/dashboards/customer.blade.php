<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-600 tracking-wide">
            🎫 Customer Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div
                x-data="ticketFlow(@js([
                    'initialStep' => session()->has('ticket_submitted') ? 'success' : (old('type') ? 'form' : 'cards'),
                    'initialType' => old('type'),
                    'form' => [
                        'subject' => old('subject', ''),
                        'details' => old('details', ''),
                        'productId' => old('product_id'),
                    ],
                ]))"
                class="bg-white rounded-2xl shadow-xl p-10 relative overflow-hidden"
            >
                <!-- Step 1: Card Selection -->
                <template x-if="step === 'cards'">
                    <div
                        class="grid md:grid-cols-2 gap-8 relative z-10"
                        x-init="$el.classList.add('animate-fade-in')"
                    >
                        <!-- Sales Card -->
                        <div @click="selectType('sale')"
                             class="cursor-pointer group p-8 rounded-2xl bg-white shadow-lg border border-gray-100 hover:scale-105 transition-transform duration-300 hover:shadow-xl opacity-0 animate-fade-in-up">
                            <div class="text-center">
                                <div class="bg-indigo-100 text-indigo-600 w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-4 group-hover:bg-indigo-200 transition-colors">
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
                                <div class="bg-pink-100 text-pink-600 w-16 h-16 flex items-center justify-center rounded-full mx-auto mb-4 group-hover:bg-pink-200 transition-colors">
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
                            <h3 class="text-xl font-semibold text-indigo-600" x-text="selectedType === 'sale' ? 'Sales Enquiry' : 'Customer Support'"></h3>
                            <button @click="cancelForm" class="text-sm text-gray-500 hover:text-red-500 transition">✖ Cancel</button>
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

                        <form method="POST" action="{{ route('customer.tickets.store') }}" class="mt-6 space-y-5">
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
                                    <p class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
                                        No products are available yet. Please contact the administrator.
                                    </p>
                                @else
                                    <select name="product_id" x-model="form.productId" required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition">
                                        <option value="" disabled x-bind:selected="!form.productId">Select a product</option>
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
                            <button type="submit"
                                @if ($products->isEmpty()) disabled @endif
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
                        <h3 class="text-2xl font-bold text-green-600">{{ session('ticket_submitted', 'Your ticket has been submitted!') }}</h3>
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
                this.form = { subject: '', details: '', productId: '' };
                this.step = 'cards';
                this.selectedType = null;
            },
            resetFlow() {
                window.location.href = '{{ route('customer.dashboard') }}';
            }
        }
    }
</script>

<!-- Animations -->
<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.7s ease-out forwards;
    }
    .delay-200 {
        animation-delay: 0.2s;
    }
</style>
