<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-600 tracking-wide">
            🎫 Customer Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div 
                x-data="ticketFlow()" 
                class="bg-white rounded-2xl shadow-xl p-10 relative overflow-hidden"
            >
                <!-- Step 1: Card Selection -->
                <template x-if="step === 'cards'">
                    <div 
                        class="grid md:grid-cols-2 gap-8 relative z-10"
                        x-init="$el.classList.add('animate-fade-in')"
                    >
                        <!-- Sales Card -->
                        <div @click="selectType('sales')" 
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
                            <h3 class="text-xl font-semibold text-indigo-600" x-text="selectedType === 'sales' ? 'Sales Enquiry' : 'Customer Support'"></h3>
                            <button @click="cancelForm" class="text-sm text-gray-500 hover:text-red-500 transition">✖ Cancel</button>
                        </div>
                        
                        <form @submit.prevent="submitForm" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium mb-1">Subject</label>
                                <input type="text" x-model="form.subject" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 placeholder-gray-400 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Details</label>
                                <textarea rows="4" x-model="form.details" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 placeholder-gray-400 transition"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-md transition transform hover:scale-105">
                                📩 Submit Ticket
                            </button>
                        </form>
                    </div>
                </template>

                <!-- Step 3: Success Message -->
                <template x-if="step === 'success'">
                    <div x-transition class="text-center relative z-10">
                        <div class="text-6xl mb-4">✅</div>
                        <h3 class="text-2xl font-bold text-green-600">Your ticket has been submitted!</h3>
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
    function ticketFlow() {
        return {
            step: 'cards',
            selectedType: null,
            form: { subject: '', details: '' },
            selectType(type) {
                this.selectedType = type;
                this.step = 'form';
            },
            cancelForm() {
                this.form = { subject: '', details: '' };
                this.step = 'cards';
            },
            submitForm() {
                // You can hook Livewire or AJAX here
                this.step = 'success';
            },
            resetFlow() {
                this.form = { subject: '', details: '' };
                this.step = 'cards';
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
