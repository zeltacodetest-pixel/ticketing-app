<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
   public function register(): void
{
    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    ]);

    $validated['password'] = Hash::make($validated['password']);

    event(new Registered($user = User::create($validated)));

    // 👇 Auto assign Customer role
    $user->assignRole('Customer');

    Auth::login($user);

    $this->redirect(route('dashboard', absolute: false), navigate: true);
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
                    Ticketing System Registration
                </p>
            </div>

            <!-- Register Form -->
            <form wire:submit="register" class="space-y-6">
                <!-- Name -->
                <div class="group">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input wire:model="name" id="name" type="text" required autofocus
                        placeholder="Enter your full name"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 transition-all duration-300 px-4 py-2.5 group-hover:shadow-md">
                </div>

                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input wire:model="email" id="email" type="email" required
                        placeholder="your@email.com"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 transition-all duration-300 px-4 py-2.5 group-hover:shadow-md">
                </div>

                <!-- Password -->
                <div class="group">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input wire:model="password" id="password" type="password" required
                        placeholder="••••••••"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 transition-all duration-300 px-4 py-2.5 group-hover:shadow-md">
                </div>

                <!-- Confirm Password -->
                <div class="group">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" required
                        placeholder="Re-enter password"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-800 placeholder-gray-400 transition-all duration-300 px-4 py-2.5 group-hover:shadow-md">
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

    <!-- Tailwind Animations -->
    <style>
    @keyframes fade-in { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform: translateY(0);} }
    @keyframes slide-down { from { opacity:0; transform: translateY(-15px);} to { opacity:1; transform: translateY(0);} }

    .animate-fade-in { animation: fade-in 0.8s ease forwards; }
    .animate-fade-in-slow { animation: fade-in 1.2s ease forwards; }
    .animate-slide-down { animation: slide-down 0.9s ease forwards; }
    </style>
</div>




