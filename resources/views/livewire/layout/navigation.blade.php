<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $user = auth()->user();
    $dashboardRouteName = $user ? $user->dashboardRouteName() : 'customer.dashboard';
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Section -->
            <div class="flex items-center space-x-8">
                <!-- Branding -->
                <a href="{{ route($dashboardRouteName) }}" wire:navigate class="flex items-center">
                    <span class="text-2xl font-extrabold tracking-wide text-indigo-600">ZeltaCode</span>
                </a>

                <!-- Desktop Links -->
                <div class="hidden sm:flex space-x-6">
                    <x-nav-link :href="route($dashboardRouteName)" :active="request()->routeIs($dashboardRouteName)" wire:navigate> {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Section -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- Profile Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-600 hover:text-indigo-600 focus:outline-none transition">
                            <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name">
                            </span>
                            <svg class="ml-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M6 8l4 4 4-4" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-indigo-600 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-3 pb-4 space-y-1">
            <x-responsive-nav-link :href="route($dashboardRouteName)" :active="request()->routeIs($dashboardRouteName)" wire:navigate>
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Profile Section -->
        <div class="pt-4 pb-1 border-t border-gray-100">
            <div class="px-4">
                <div class="font-semibold text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
