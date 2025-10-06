<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $country = '';
    public string $phone = '';
    public string $company = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'country' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        // 👇 Auto assign Customer role
        $user->assignRole('Customer');

        Auth::login($user);

        $this->redirect(route($user->dashboardRouteName(), absolute: false), navigate: true);
    }
}; ?>

<div class="flex justify-center items-center min-h-screen bg-white-50">
    <div class="w-full sm:w-[70%] lg:w-[50%] bg-white  animate-fade-in px-8 py-10">
        <!-- Custom Branding -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-indigo-600 tracking-wide animate-slide-down">
                ZeltaCode
            </h1>
            <p class="text-sm text-gray-500 mt-1 animate-fade-in-slow">
                Ticketing System Registration
            </p>
        </div>

        <!-- Register Form -->
        <form wire:submit="register" class="space-y-6">
            <!-- Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input wire:model="first_name" id="first_name" type="text" required autofocus
                        placeholder="First Name"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input wire:model="last_name" id="last_name" type="text" required
                        placeholder="Last Name"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input wire:model="email" id="email" type="email" required placeholder="your@email.com"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
            </div>

            <!-- Country & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input wire:model="country" id="country" type="text" placeholder="Country"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input wire:model="phone" id="phone" type="tel" placeholder="+1 555 123 4567"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
                </div>
            </div>

            <!-- Company -->
            <div>
                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                <input wire:model="company" id="company" type="text" placeholder="Your organization"
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
            </div>

            <!-- Passwords -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input wire:model="password" id="password" type="password" required placeholder="••••••••"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" required
                        placeholder="Re-enter password"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 px-4 py-3">
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl px-4 py-3 transition-all duration-300 transform hover:scale-[1.03] focus:ring focus:ring-indigo-300 hover:shadow-lg">
                Register
            </button>
        </form>

        <!-- Footer -->
        <p class="mt-6 text-center text-sm text-gray-600 animate-fade-in-slow">
            Already have an account?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">Sign in</a>
        </p>
    </div>
</div>

