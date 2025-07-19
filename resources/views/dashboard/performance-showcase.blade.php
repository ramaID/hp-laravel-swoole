<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>High-Performance Laravel with Swoole - Performance Showcase</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root.dark {
            color-scheme: dark;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .performance-card {
            transition: all 0.3s ease;
        }

        .performance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
    <!-- Navigation Header -->
    <nav class="bg-black/20 backdrop-blur-md border-b border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div
                            class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 100 125" x="0px"
                                y="0px">
                                <title>Artboard 10</title>
                                <path
                                    d="M3.15,60.3A39.61,39.61,0,0,1,37.54,18.45V12.94H33.13a1.49,1.49,0,0,1-1.49-1.49V4.25a1.49,1.49,0,0,1,1.49-1.49h19a1.49,1.49,0,0,1,1.49,1.49v7.19a1.49,1.49,0,0,1-1.49,1.49H47.72v5.51A39.66,39.66,0,0,1,81.21,48.91a8.51,8.51,0,0,0-4.78-1.47H73.65a8.48,8.48,0,0,0-2.06.27A30.63,30.63,0,1,0,51.93,86.86a8.5,8.5,0,0,0,2.4,7.23l1,1A39.54,39.54,0,0,1,3.15,60.3Zm58.1-15.71a2.65,2.65,0,0,0-3,3.89,2.66,2.66,0,0,0,3.62,1h0a2.65,2.65,0,0,0-.65-4.86Zm-7-7.32a2.66,2.66,0,1,0,1,3.63,2.65,2.65,0,0,0-1-3.63Zm-29,28.13a2.65,2.65,0,1,0,1.62,1.23A2.63,2.63,0,0,0,25.32,65.39Zm8.26,7.85a2.65,2.65,0,1,0-1.33,4.95,2.65,2.65,0,0,0,2.56-3.35A2.62,2.62,0,0,0,33.58,73.24Zm9.05,7.71h0a2.66,2.66,0,0,0,2.65-2.66,2.63,2.63,0,0,0-.78-1.87,2.58,2.58,0,0,0-1.88-.77h0A2.65,2.65,0,0,0,40,78.3,2.66,2.66,0,0,0,42.63,80.95ZM45,57.54V36.79a2.39,2.39,0,0,0-2.39-2.39A23.17,23.17,0,0,0,19.45,57.54a2.39,2.39,0,0,0,2.39,2.39H42.59A2.39,2.39,0,0,0,45,57.54ZM96.94,74v2.78a2.52,2.52,0,0,1-2.09,2.48l-3.28.58a17,17,0,0,1-1.7,4.09l1.91,2.73a2.52,2.52,0,0,1-.28,3.23l-2,2a2.52,2.52,0,0,1-3.23.28l-2.73-1.91a17,17,0,0,1-4.09,1.7l-.58,3.28a2.52,2.52,0,0,1-2.49,2.09H73.65a2.52,2.52,0,0,1-2.49-2.09l-.58-3.28a17,17,0,0,1-4.09-1.7l-2.73,1.91a2.52,2.52,0,0,1-3.23-.28l-2-2a2.52,2.52,0,0,1-.28-3.23l1.91-2.73a17,17,0,0,1-1.7-4.09l-3.28-.58a2.52,2.52,0,0,1-2.09-2.48V74a2.52,2.52,0,0,1,2.09-2.48l3.28-.58a17,17,0,0,1,1.7-4.09l-1.91-2.73a2.52,2.52,0,0,1,.28-3.23l2-2a2.52,2.52,0,0,1,3.23-.28l2.73,1.91a17,17,0,0,1,4.09-1.7l.58-3.28a2.52,2.52,0,0,1,2.49-2.09h2.78a2.52,2.52,0,0,1,2.49,2.09l.58,3.28a17,17,0,0,1,4.09,1.7l2.73-1.91a2.52,2.52,0,0,1,3.23.28l2,2a2.52,2.52,0,0,1,.28,3.23L89.88,66.8a17,17,0,0,1,1.7,4.09l3.28.58A2.52,2.52,0,0,1,96.94,74Zm-16.35-.5a1.12,1.12,0,0,0-.81-.59h-.13l-4.27-.2,1-6.06a.83.83,0,0,0-1.56-.51L69.47,76.3a1.11,1.11,0,0,0,0,1,1.13,1.13,0,0,0,.81.59h.13l4.27.2-1,6.06a.83.83,0,0,0,1.55.51l5.36-10.23A1.11,1.11,0,0,0,80.59,73.45Z" />
                                <text x="0" y="115" fill="#000000" font-size="5px" font-weight="bold"
                                    font-family="'Helvetica Neue', Helvetica, Arial-Unicode, Arial, Sans-serif">Created
                                    by Gregor Cresnar</text><text x="0" y="120" fill="#000000" font-size="5px"
                                    font-weight="bold"
                                    font-family="'Helvetica Neue', Helvetica, Arial-Unicode, Arial, Sans-serif">from the
                                    Noun Project</text>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-white">Laravel + Swoole</h1>
                            <p class="text-xs text-purple-300">High-Performance Dashboard</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-sm text-green-400">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span>Swoole Active</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                Performance
                <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Showcase
                </span>
            </h1>
            <p class="text-xl text-purple-200 mb-6 max-w-3xl mx-auto">
                Experience the power of Laravel Octane with Swoole. See real-time performance metrics
                across different optimization strategies.
            </p>
            <div class="flex justify-center space-x-4">
                <div class="bg-white/10 backdrop-blur-md rounded-lg px-4 py-2 border border-white/20">
                    <span class="text-purple-300 text-sm">Runtime:</span>
                    <span class="text-white font-mono ml-2">Swoole</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg px-4 py-2 border border-white/20">
                    <span class="text-purple-300 text-sm">Concurrency:</span>
                    <span class="text-white font-mono ml-2">Enabled</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg px-4 py-2 border border-white/20">
                    <span class="text-purple-300 text-sm">Cache:</span>
                    <span class="text-white font-mono ml-2">Octane Store</span>
                </div>
            </div>
        </div>

        <!-- Performance Comparison Cards -->
        <div class="grid gap-8 lg:grid-cols-2 xl:grid-cols-4 mb-12">
            @foreach ($performance_metrics as $key => $item)
                <div class="performance-card bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-{{ $item['color'] }}-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-{{ $item['color'] }}-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-{{ $item['color'] }}-400 text-sm font-medium">{{ $item['title'] }}</div>
                            <div class="text-{{ $item['color'] }}-300 text-xs">{{ $item['description'] }}</div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="text-2xl font-bold text-white mb-1">
                            {{ $item['avg_response_time'] }}</div>
                        <div class="text-purple-300 text-sm">Average Response</div>
                    </div>
                    <a href="{{ $item['url'] }}"
                        class="inline-flex items-center text-red-400 hover:text-red-300 text-sm font-medium transition-colors">
                        View Dashboard
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Performance Benefits -->
        <div class="grid gap-8 lg:grid-cols-3 mb-12">
            <div class="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-white/10">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-500/20 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">35,000x Faster</h3>
                        <p class="text-purple-300">Than traditional PHP</p>
                    </div>
                </div>
                <p class="text-purple-200 text-sm">
                    Swoole's persistent connections and memory resident architecture
                    eliminates the bootstrap overhead. Real M1 chip results: 3s → 85μs response time.
                </p>
            </div>

            <div class="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-white/10">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-green-500/20 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Concurrent Tasks</h3>
                        <p class="text-green-300">Parallel processing</p>
                    </div>
                </div>
                <p class="text-green-200 text-sm">
                    Execute multiple database queries simultaneously using
                    Laravel Octane's concurrent task execution.
                </p>
            </div>

            <div class="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-white/10">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-500/20 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Memory Cache</h3>
                        <p class="text-blue-300">Persistent storage</p>
                    </div>
                </div>
                <p class="text-blue-200 text-sm">
                    Share data between requests using Octane's in-memory
                    cache store for ultra-fast data retrieval.
                </p>
            </div>
        </div>

        <!-- Technical Specifications -->
        <div class="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-white/10 mb-12">
            <h3 class="text-2xl font-bold text-white mb-6">Technical Specifications</h3>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2">100K+</div>
                    <div class="text-purple-300 text-sm">Events Processed</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2">85μs</div>
                    <div class="text-green-300 text-sm">Tick Cache Response</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2">111μs</div>
                    <div class="text-blue-300 text-sm">Memory Cache Response</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-400 mb-2">99.9%</div>
                    <div class="text-yellow-300 text-sm">Uptime</div>
                </div>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="bg-white/5 backdrop-blur-md rounded-xl p-8 border border-white/10">
            <h3 class="text-2xl font-bold text-white mb-6">API Endpoints</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center space-x-4">
                        <span
                            class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-mono">GET</span>
                        <span class="text-white font-mono">/swoole-stats</span>
                    </div>
                    <div class="text-purple-300 text-sm">Server Statistics</div>
                </div>
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-mono">GET</span>
                        <span class="text-white font-mono">/test-ticker</span>
                    </div>
                    <div class="text-purple-300 text-sm">Cache Warming</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black/20 backdrop-blur-md border-t border-white/10 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-purple-300 text-sm">
                    Built with ❤️ using Laravel Octane + Swoole
                </p>
                <p class="text-purple-400 text-xs mt-2">
                    High-Performance PHP • Memory Resident • Concurrent Processing
                </p>
            </div>
        </div>
    </footer>
</body>

</html>
