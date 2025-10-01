<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="w-full max-w-md animate-fade-in">
        <!-- Card -->
        <div class="px-8 py-10">
            <!-- Custom Branding -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-extrabold text-indigo-600 tracking-wide animate-slide-down">
                    ZeltaCode
                </h1>
                <p class="text-sm text-gray-500 mt-1 animate-fade-in-slow">
                    Ticketing System Login
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form wire:submit="login" class="space-y-6">
                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input wire:model="form.email" id="email" type="email" required autofocus
                        placeholder="your@email.com"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 transition-all duration-300 px-4 py-2.5 group-hover:shadow-md">
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="group">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input wire:model="form.password" id="password" type="password" required
                        placeholder="••••••••"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 transition-all duration-300 px-4 py-2.5 group-hover:shadow-md">
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-sm text-indigo-600 hover:underline font-medium">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl px-4 py-3 transition-all duration-300 transform hover:scale-[1.03] focus:ring focus:ring-indigo-300 hover:shadow-lg">
                    Log in
                </button>
            </form>

            <!-- Footer -->
            <p class="mt-6 text-center text-sm text-gray-600 animate-fade-in-slow">
                Don’t have an account? 
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">Register</a>
            </p>
        </div>
    </div>

    <!-- Tailwind Animations -->
    <style>
        @keyframes fade-in { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform: translateY(0);} }
        @keyframes slide-down { from { opacity:0; transform: translateY(-15px);} to { opacity:1; transform: translateY(0);} }

        .animate-fade-in { animation: fade-in 0.8s ease forwards; }
        .animate-fade-in-slow { animation: fade-in 1.2s ease forwards; }
        .animate-slide-down { animation: slide-down 0.9s ease forwards; }
    </style>
</div>

