<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laravel Octane + Swoole - Real-time Metrics</title>

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
        .metric-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
        }
        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .performance-gauge {
            background: conic-gradient(from 0deg, #ef4444, #f59e0b, #10b981);
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
        }
    </style>

    <script>
        // Auto-refresh functionality
        let refreshInterval;

        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                window.location.reload();
            }, 5000); // Refresh every 5 seconds
        }

        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }

        // Start auto-refresh when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();

            // Add visibility change handler to pause refresh when tab is not visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoRefresh();
                } else {
                    startAutoRefresh();
                }
            });
        });
    </script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    <!-- Header with Live Status -->
    <header class="bg-black/30 backdrop-blur-md border-b border-white/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-white">Real-time Metrics</h1>
                    <div class="flex items-center space-x-2 text-sm">
                        <div class="w-2 h-2 bg-green-400 rounded-full pulse-animation"></div>
                        <span class="text-green-400">Live</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-white/70">
                        Last updated: <span class="text-white">{{ now()->format('H:i:s') }}</span>
                    </div>
                    <button onclick="window.location.reload()"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                        Refresh Now
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Swoole Server Status -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white mb-6">Swoole Server Status</h2>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="metric-card rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-green-400 text-sm font-medium">Uptime</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white mb-2">
                        {{ gmdate('H:i:s', time() - ($swoole_stats['start_time'] ?? time())) }}
                    </div>
                    <div class="text-white/70 text-sm">Server running</div>
                </div>

                <div class="metric-card rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-blue-400 text-sm font-medium">Connections</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white mb-2">
                        {{ number_format($swoole_stats['connection_num'] ?? 0) }}
                    </div>
                    <div class="text-white/70 text-sm">Active connections</div>
                </div>

                <div class="metric-card rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-purple-400 text-sm font-medium">Requests</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white mb-2">
                        {{ number_format($swoole_stats['request_count'] ?? 0) }}
                    </div>
                    <div class="text-white/70 text-sm">Total processed</div>
                </div>

                <div class="metric-card rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-yellow-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-yellow-400 text-sm font-medium">Workers</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white mb-2">
                        {{ $swoole_stats['idle_worker_num'] ?? 0 }}/{{ $swoole_stats['worker_num'] ?? 1 }}
                    </div>
                    <div class="text-white/70 text-sm">Idle/Total workers</div>
                </div>
            </div>
        </div>

        <!-- Performance Comparison -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white mb-6">Performance Comparison</h2>
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Response Time Chart -->
                <div class="metric-card rounded-xl p-8 border border-white/20">
                    <h3 class="text-xl font-bold text-white mb-6">Response Time Comparison</h3>
                    <div class="space-y-4">
                        @foreach($performance_metrics as $key => $metric)
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full bg-{{ $metric['color'] }}-500"></div>
                                <div>
                                    <div class="text-white font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                    <div class="text-{{ $metric['color'] }}-400 text-sm">{{ $metric['description'] }}</div>
                                </div>
                            </div>
                            <div class="text-white font-mono text-lg">{{ $metric['avg_response_time'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Cache Status -->
                <div class="metric-card rounded-xl p-8 border border-white/20">
                    <h3 class="text-xl font-bold text-white mb-6">Cache Status</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full {{ $cache_stats['tick_cache_exists'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                <div class="text-white">Tick Cache</div>
                            </div>
                            <div class="text-{{ $cache_stats['tick_cache_exists'] ? 'green' : 'red' }}-400">
                                {{ $cache_stats['tick_cache_exists'] ? 'Active' : 'Inactive' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full {{ $cache_stats['events_cache_exists'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                <div class="text-white">Events Cache</div>
                            </div>
                            <div class="text-{{ $cache_stats['events_cache_exists'] ? 'green' : 'red' }}-400">
                                {{ $cache_stats['events_cache_exists'] ? 'Active' : 'Inactive' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                                <div class="text-white">Total Cached Items</div>
                            </div>
                            <div class="text-blue-400">{{ $cache_stats['total_cached_items'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white mb-6">Quick Actions</h2>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <a href="/dashboard-sequential"
                   class="metric-card rounded-xl p-6 border border-white/20 hover:border-red-500/50 transition-all text-center group">
                    <div class="p-3 bg-red-500/20 rounded-lg mx-auto w-fit mb-4 group-hover:bg-red-500/30 transition-colors">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-white font-medium mb-2">Sequential Dashboard</div>
                    <div class="text-red-400 text-sm">Traditional approach</div>
                </a>

                <a href="/dashboard-concurrent"
                   class="metric-card rounded-xl p-6 border border-white/20 hover:border-yellow-500/50 transition-all text-center group">
                    <div class="p-3 bg-yellow-500/20 rounded-lg mx-auto w-fit mb-4 group-hover:bg-yellow-500/30 transition-colors">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="text-white font-medium mb-2">Concurrent Dashboard</div>
                    <div class="text-yellow-400 text-sm">Parallel processing</div>
                </a>

                <a href="/dashboard-cached"
                   class="metric-card rounded-xl p-6 border border-white/20 hover:border-blue-500/50 transition-all text-center group">
                    <div class="p-3 bg-blue-500/20 rounded-lg mx-auto w-fit mb-4 group-hover:bg-blue-500/30 transition-colors">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                        </svg>
                    </div>
                    <div class="text-white font-medium mb-2">Cached Dashboard</div>
                    <div class="text-blue-400 text-sm">Memory cached</div>
                </a>

                <a href="/dashboard-tick-cached"
                   class="metric-card rounded-xl p-6 border border-white/20 hover:border-green-500/50 transition-all text-center group">
                    <div class="p-3 bg-green-500/20 rounded-lg mx-auto w-fit mb-4 group-hover:bg-green-500/30 transition-colors">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-white font-medium mb-2">Tick Cache Dashboard</div>
                    <div class="text-green-400 text-sm">Pre-warmed cache</div>
                </a>
            </div>
        </div>

        <!-- API Status -->
        <div class="metric-card rounded-xl p-8 border border-white/20">
            <h2 class="text-3xl font-bold text-white mb-6">API Endpoints</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="p-4 bg-white/5 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white font-mono">/swoole-stats</span>
                        <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm">GET</span>
                    </div>
                    <p class="text-white/70 text-sm">Get real-time Swoole server statistics</p>
                    <a href="/swoole-stats" target="_blank"
                       class="inline-flex items-center mt-2 text-blue-400 hover:text-blue-300 text-sm">
                        View JSON
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>

                <div class="p-4 bg-white/5 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white font-mono">/test-ticker</span>
                        <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm">GET</span>
                    </div>
                    <p class="text-white/70 text-sm">Manually trigger cache warming for tick cache</p>
                    <a href="/test-ticker" target="_blank"
                       class="inline-flex items-center mt-2 text-blue-400 hover:text-blue-300 text-sm">
                        Trigger Cache
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
