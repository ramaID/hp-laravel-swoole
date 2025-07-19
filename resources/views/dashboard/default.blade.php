<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Laravel Dashboard' }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <div class="[grid-area:main] p-6 lg:p-8 [[data-flux-container]_&]:px-0 p-6" data-flux-main="">
        <div class="flex h-full w-full flex-1 flex-col gap-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium [:where(&)]:text-zinc-800 [:where(&)]:dark:text-white text-sm [&:has(+[data-flux-subheading])]:mb-2 [[data-flux-subheading]+&]:mt-2 text-zinc-900 dark:text-zinc-100"
                        data-flux-heading="">Dashboard Statistics</div>
                    <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 text-zinc-600 dark:text-zinc-400 mt-1"
                        data-flux-text="">Real-time performance metrics and event monitoring</div>
                </div>
                <div class="text-right">
                    <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 text-sm text-zinc-500 dark:text-zinc-400"
                        data-flux-text="">Cache Status</div>
                    <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 block text-sm font-medium text-emerald-600 dark:text-emerald-400"
                        data-flux-text="">Data loaded from cache</div>
                </div>
            </div>

            <!-- Main Statistics Grid -->
            <div class="grid gap-6 grid-cols-1 md:grid-cols-3">
                <!-- Execution Time Card -->
                <div class="bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 [:where(&)]:p-6 [:where(&)]:rounded-xl p-6"
                    data-flux-card="">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 text-zinc-600 dark:text-zinc-400 text-sm"
                                data-flux-text="">Execution Time</div>
                            <div class="font-medium [:where(&)]:text-zinc-800 [:where(&)]:dark:text-white text-base [&:has(+[data-flux-subheading])]:mb-2 [[data-flux-subheading]+&]:mt-2 tabular-nums text-zinc-900 dark:text-zinc-100"
                                data-flux-heading="">{{ $execution_time }}</div>
                        </div>
                    </div>
                </div>

                <!-- Total Events Card -->
                <div class="bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 [:where(&)]:p-6 [:where(&)]:rounded-xl p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-700"
                    data-flux-card="">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 text-blue-700 dark:text-blue-300 font-medium"
                                data-flux-text="">Total Events</div>
                            <div class="font-medium [:where(&)]:text-zinc-800 [:where(&)]:dark:text-white text-sm [&:has(+[data-flux-subheading])]:mb-2 [[data-flux-subheading]+&]:mt-2 mt-2 tabular-nums text-blue-900 dark:text-blue-100"
                                data-flux-heading="">{{ number_format($total_count) }}</div>
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
                </div>

                <!-- Info Events Card -->
                <div class="bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 [:where(&)]:p-6 [:where(&)]:rounded-xl p-6 bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border-emerald-200 dark:border-emerald-700"
                    data-flux-card="">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 text-emerald-700 dark:text-emerald-300 font-medium"
                                data-flux-text="">Info Events</div>
                            <div class="font-medium [:where(&)]:text-zinc-800 [:where(&)]:dark:text-white text-sm [&:has(+[data-flux-subheading])]:mb-2 [[data-flux-subheading]+&]:mt-2 mt-2 tabular-nums text-emerald-900 dark:text-emerald-100"
                                data-flux-heading="">{{ number_format($info_count) }}</div>
                        </div>
                        <div class="p-3 bg-emerald-200 dark:bg-emerald-700 rounded-full">
                            <svg class="w-6 h-6 text-emerald-700 dark:text-emerald-200" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                <!-- Warning Events Card -->
                <div class="bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 [:where(&)]:p-6 [:where(&)]:rounded-xl p-6 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border-amber-200 dark:border-amber-700"
                    data-flux-card="">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 text-amber-700 dark:text-amber-300 font-medium"
                                data-flux-text="">Warning Events</div>
                            <div class="font-medium [:where(&)]:text-zinc-800 [:where(&)]:dark:text-white text-sm [&:has(+[data-flux-subheading])]:mb-2 [[data-flux-subheading]+&]:mt-2 mt-2 tabular-nums text-amber-900 dark:text-amber-100"
                                data-flux-heading="">{{ number_format($warning_count) }}</div>
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
                </div>

                <!-- Alert Events Card -->
                <div class="bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 [:where(&)]:p-6 [:where(&)]:rounded-xl p-6 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-red-200 dark:border-red-700"
                    data-flux-card="">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="[:where(&)]:text-sm [:where(&)]:text-zinc-500 [:where(&)]:dark:text-white/70 text-red-700 dark:text-red-300 font-medium"
                                data-flux-text="">Alert Events</div>
                            <div class="font-medium [:where(&)]:text-zinc-800 [:where(&)]:dark:text-white text-sm [&:has(+[data-flux-subheading])]:mb-2 [[data-flux-subheading]+&]:mt-2 mt-2 tabular-nums text-red-900 dark:text-red-100"
                                data-flux-heading="">{{ number_format($alert_count) }}</div>
                        </div>
                        <div class="p-3 bg-red-200 dark:bg-red-700 rounded-full">
                            <svg class="w-6 h-6 text-red-700 dark:text-red-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
