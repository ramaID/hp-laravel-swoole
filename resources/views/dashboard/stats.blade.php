<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main class="p-6">
        <div class="flex h-full w-full flex-1 flex-col gap-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="2xl" class="text-zinc-900 dark:text-zinc-100">Dashboard Statistics
                    </flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400 mt-1">Real-time performance metrics and event
                        monitoring</flux:text>
                </div>
                <div class="text-right">
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Cache Status</flux:text>
                    <flux:text
                        class="block text-sm font-medium {{ str_contains($cache_status, 'not yet warmed') ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ $cache_status }}
                    </flux:text>
                </div>
            </div>

            <!-- Main Statistics Grid -->
            <div class="grid gap-6 grid-cols-1 md:grid-cols-3">
                <!-- Execution Time Card -->
                <flux:card class="p-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <flux:text class="text-zinc-600 dark:text-zinc-400 text-sm">Execution Time</flux:text>
                            <flux:heading size="lg" class="tabular-nums text-zinc-900 dark:text-zinc-100">
                                {{ $execution_time }}</flux:heading>
                        </div>
                    </div>
                </flux:card>

                <!-- Total Events Card -->
                <flux:card
                    class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="text-blue-700 dark:text-blue-300 font-medium">Total Events</flux:text>
                            <flux:heading size="2xl" class="mt-2 tabular-nums text-blue-900 dark:text-blue-100">
                                {{ number_format($total_count) }}</flux:heading>
                        </div>
                        <div class="p-3 bg-blue-200 dark:bg-blue-700 rounded-full">
                            <svg class="w-6 h-6 text-blue-700 dark:text-blue-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </flux:card>

                <!-- Info Events Card -->
                <flux:card
                    class="p-6 bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border-emerald-200 dark:border-emerald-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="text-emerald-700 dark:text-emerald-300 font-medium">Info Events
                            </flux:text>
                            <flux:heading size="2xl"
                                class="mt-2 tabular-nums text-emerald-900 dark:text-emerald-100">
                                {{ number_format($info_count) }}</flux:heading>
                        </div>
                        <div class="p-3 bg-emerald-200 dark:bg-emerald-700 rounded-full">
                            <svg class="w-6 h-6 text-emerald-700 dark:text-emerald-200" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </flux:card>
            </div>

            <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                <!-- Warning Events Card -->
                <flux:card
                    class="p-6 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border-amber-200 dark:border-amber-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="text-amber-700 dark:text-amber-300 font-medium">Warning Events</flux:text>
                            <flux:heading size="2xl" class="mt-2 tabular-nums text-amber-900 dark:text-amber-100">
                                {{ number_format($warning_count) }}</flux:heading>
                        </div>
                        <div class="p-3 bg-amber-200 dark:bg-amber-700 rounded-full">
                            <svg class="w-6 h-6 text-amber-700 dark:text-amber-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </flux:card>

                <!-- Alert Events Card -->
                <flux:card
                    class="p-6 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-red-200 dark:border-red-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="text-red-700 dark:text-red-300 font-medium">Alert Events</flux:text>
                            <flux:heading size="2xl" class="mt-2 tabular-nums text-red-900 dark:text-red-100">
                                {{ number_format($alert_count) }}</flux:heading>
                        </div>
                        <div class="p-3 bg-red-200 dark:bg-red-700 rounded-full">
                            <svg class="w-6 h-6 text-red-700 dark:text-red-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </flux:card>
            </div>

            {{-- <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                <!-- Event Distribution Percentages -->
                <flux:card class="p-6">
                    <flux:text class="text-zinc-600 dark:text-zinc-400 text-sm mb-4">Event Distribution</flux:text>
                    <div class="space-y-3">
                        @php
                            $total = max($total_count, 1); // Prevent division by zero
                            $infoPercent = round(($info_count / $total) * 100, 1);
                            $warningPercent = round(($warning_count / $total) * 100, 1);
                            $alertPercent = round(($alert_count / $total) * 100, 1);
                        @endphp

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                <flux:text class="text-sm">Info Events</flux:text>
                            </div>
                            <flux:text class="text-sm font-medium tabular-nums">{{ $infoPercent }}%</flux:text>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                                <flux:text class="text-sm">Warning Events</flux:text>
                            </div>
                            <flux:text class="text-sm font-medium tabular-nums">{{ $warningPercent }}%</flux:text>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <flux:text class="text-sm">Alert Events</flux:text>
                            </div>
                            <flux:text class="text-sm font-medium tabular-nums">{{ $alertPercent }}%</flux:text>
                        </div>
                    </div>
                </flux:card>

                <!-- Additional Info Section -->
                @if ($total_count > 0)
                    <flux:card class="p-6">
                        <div class="grid gap-4 grid-cols-3 md:grid-cols-3">
                            <div class="text-center">
                                <flux:text class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 block">
                                    {{ $total_count > 0 ? round(($info_count / $total_count) * 100) : 0 }}%
                                </flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Success Rate
                                </flux:text>
                            </div>
                            <div class="text-center">
                                <flux:text class="text-2xl font-bold text-amber-600 dark:text-amber-400 block">
                                    {{ $total_count > 0 ? round(($warning_count / $total_count) * 100) : 0 }}%
                                </flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Warnings</flux:text>
                            </div>
                            <div class="text-center">
                                <flux:text class="text-2xl font-bold text-red-600 dark:text-red-400 block">
                                    {{ $total_count > 0 ? round(($alert_count / $total_count) * 100) : 0 }}%
                                </flux:text>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Critical Issues
                                </flux:text>
                            </div>
                        </div>
                    </flux:card>
                @endif
            </div> --}}
        </div>
    </flux:main>

    @fluxScripts
</body>

</html>
