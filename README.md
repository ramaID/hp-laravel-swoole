# High Performance Laravel Application with Octane & Swoole

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Octane](https://img.shields.io/badge/Laravel-Octane-FF2D20?style=flat&logo=laravel)](https://laravel.com/docs/octane)
[![Swoole](https://img.shields.io/badge/Swoole-5.x-blue?style=flat)](https://www.swoole.co.uk/)
[![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=flat&logo=docker)](https://laravel.com/docs/sail)
[![Coverage](https://sonar.malescast.tech/api/project_badges/measure?project=hp-laravel-swoole&metric=coverage&token=sqb_0d53605a530253c84f2150cb60b215e4bd59dbb5)](https://sonar.malescast.tech/dashboard?id=hp-laravel-swoole)

> **Part 5 of the Laravel Octane + Swoole Series**: Production Deployment & Best Practices

This repository demonstrates a high-performance Laravel application powered by **Laravel Octane** with **Swoole**, showcasing significant speed improvements and advanced features like concurrency, asynchronous workflows, and production-ready optimizations.

## 📚 Series Overview

This project is part of a comprehensive blog series on building high-performance Laravel applications:

1. **[Introduction to Laravel Octane & Swoole](https://qisthi.dev/blog/hp-octane-swole-01-laravel-octane-swoole-introduction)** - Understanding the fundamentals and performance benefits
2. **[Setup & Development Environment](https://qisthi.dev/blog/hp-octane-swole-02-laravel-octane-swoole-setup)** - Laravel Sail, Docker configuration, and local development
3. **[Concurrency & Asynchronous Workflows](https://qisthi.dev/blog/hp-octane-swole-03-concurrency-asynchronous-workflows)** - Parallel processing and queue management
4. **[Advanced Caching & Performance Monitoring](https://qisthi.dev/blog/hp-octane-swoole-04-advanced-caching-data-management-monitoring-performance)** - Swoole Tables, memory management, and optimization
5. **Production Deployment & Best Practices** ← _You are here_

## 🚀 What This Project Demonstrates

### Performance Improvements

- **Boot-once, serve-many** architecture vs traditional PHP-FPM
- **3x-10x faster** response times through persistent application state
- **Concurrent request handling** with Swoole workers
- **Memory-efficient** operations with shared state management

### Advanced Features Implemented

- **Concurrent Tasks**: Execute multiple operations simultaneously using `Octane::concurrently()`
- **Background Processing**: Asynchronous job queues with Redis
- **High-Speed Caching**: Swoole Tables for blazing-fast in-memory storage (2M+ ops/sec)
- **Interval Tasks**: Background scheduled operations without blocking requests
- **Memory Management**: Proper handling of stateful application lifecycle

### Production-Ready Features

- **Docker containerization** with Laravel Sail
- **Auto-reloading** in development with file watchers
- **Memory leak prevention** with worker restart strategies
- **Error handling** and graceful degradation
- **Performance monitoring** and optimization techniques

## 🛠 Technology Stack

- **Laravel 11.x** - PHP web application framework
- **Laravel Octane** - High-performance application server interface
- **Swoole 5.x** - Asynchronous & concurrent PHP extension
- **Laravel Sail** - Docker development environment
- **Redis** - In-memory data structure store for caching and queues
- **MySQL** - Primary database
- **Docker** - Containerization platform

## 📋 Prerequisites

- **Docker Desktop** (for Laravel Sail)
- **Git** (for version control)
- **Composer** (PHP dependency manager) - _optional if using Sail_

## 🚀 Quick Start

### 1. Clone and Setup

```bash
git clone https://github.com/ramaID/hp-laravel-swoole.git
cd hp-laravel-swoole

# Copy environment file
cp .env.example .env
```

### 2. Install Dependencies and Start Services

```bash
# Install Composer dependencies
composer install

# Install Laravel Sail
composer require laravel/sail --dev
php artisan sail:install

# Start Docker containers
./vendor/bin/sail up -d

# Generate application key
./vendor/bin/sail artisan key:generate

# Install Node.js dependencies for file watching
./vendor/bin/sail npm install --save-dev chokidar
```

### 3. Database Setup

```bash
# Run migrations and seed test data
./vendor/bin/sail artisan migrate:fresh --seed
```

This creates:

- **1,000 users** for testing
- **100,000 events** for performance benchmarking

### 4. Install and Configure Octane

```bash
# Install Laravel Octane
./vendor/bin/sail composer require laravel/octane

# Install Octane with Swoole
./vendor/bin/sail artisan octane:install
# Select "swoole" when prompted

# Rebuild containers with Octane configuration
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

### 5. Verify Installation

```bash
# Check if Swoole is running
curl -I http://localhost

# Look for: Server: swoole-http-server
```

Your application should now be running at **http://localhost** with Swoole!

## 🎮 Testing the Performance Showcase

### Interactive Dashboard

1. **Visit the main dashboard**: `http://localhost/performance-showcase`
2. **Explore each performance tier**:
   - Click "Sequential" to experience baseline performance
   - Click "Concurrent" to see parallel processing improvements
   - Click "Cached" to witness microsecond response times
   - Click "Tick Cache" for the ultimate performance experience

### Monitoring Tools

3. **Warm the cache**: `http://localhost/test-ticker`
   - Manually triggers background cache warming
   - Prepares tick cache for optimal performance

## 🎯 Performance Demonstrations

This application provides a comprehensive **Performance Showcase Dashboard** accessible at `/performance-showcase` (or `/` homepage) that demonstrates four distinct performance optimization levels:

### 1. Sequential Processing - The Baseline

- **Route**: `/dashboard-sequential`
- **Average Response Time**: ~3 seconds
- **Description**: Traditional synchronous processing where each database query waits for the previous one to complete
- **Use Case**: Represents standard PHP-FPM behavior

### 2. Concurrent Processing - Parallel Execution

- **Route**: `/dashboard-concurrent`
- **Average Response Time**: ~1 second
- **Description**: Uses `Octane::concurrently()` to execute multiple database queries in parallel
- **Performance Gain**: ~3x faster than sequential

```php
[$count, $eventsInfo, $eventsWarning, $eventsAlert] = Octane::concurrently([
    fn () => Event::query()->count(),
    fn () => Event::query()->ofType('INFO')->count(),
    fn () => Event::query()->ofType('WARNING')->count(),
    fn () => Event::query()->ofType('ALERT')->count(),
]);
```

### 3. Cached Processing - In-Memory Storage

- **Route**: `/dashboard-cached`
- **Average Response Time**: ~111μs (microseconds!)
- **Description**: Leverages Octane's in-memory cache with TTL to store concurrent query results
- **Performance Gain**: ~27,000x faster than sequential

### 4. Tick Cache - Pre-Warmed Background Cache

- **Route**: `/dashboard-tick-cached`
- **Average Response Time**: ~85μs (microseconds!)
- **Description**: Pre-warmed cache updated by background processes, eliminating all query overhead
- **Performance Gain**: ~35,000x faster than sequential

### Real-Time Monitoring

The application also includes:

- **Real-time Metrics Dashboard**: `/real-time-metrics` - Live Swoole server statistics
- **Swoole Stats API**: `/swoole-stats` - JSON endpoint for monitoring integration
- **Cache Warming**: `/test-ticker` - Manual trigger for background cache warming

### Performance Showcase Features

The main dashboard (`/performance-showcase`) displays:

- **Live Swoole server statistics** (connections, workers, requests)
- **Cache status monitoring** (active cache keys, hit rates)
- **Performance comparison** between all four optimization levels
- **Interactive navigation** to test each performance tier

## 🔧 Development Workflow

### File Watching & Auto-Reload

The application is configured with automatic reloading during development:

```bash
# Workers automatically restart when files change
# No manual intervention needed!
```

### Manual Worker Management

```bash
# Manually reload workers
./vendor/bin/sail artisan octane:reload

# Stop Octane
./vendor/bin/sail artisan octane:stop

# View worker status
./vendor/bin/sail artisan octane:status
```

### Queue Processing

```bash
# Start queue workers
./vendor/bin/sail artisan queue:work

# Process failed jobs
./vendor/bin/sail artisan queue:retry all
```

## 📊 Performance Benchmarks

### Response Time Improvements

| Performance Tier      | Average Response Time | Description                        | Improvement vs Sequential |
| --------------------- | --------------------- | ---------------------------------- | ------------------------- |
| Sequential Processing | ~3 seconds            | Traditional synchronous processing | Baseline                  |
| Concurrent Processing | ~1 second             | Parallel execution with Octane     | **3x faster**             |
| Cached Processing     | ~111μs                | In-memory cache with TTL           | **~27,000x faster**       |
| Tick Cache Processing | ~85μs                 | Pre-warmed background cache        | **~35,000x faster**       |

### Swoole Server Performance

The application showcases real-time Swoole server metrics:

- **Worker Management**: Automatic worker pool management and load balancing
- **Connection Handling**: Persistent connections and efficient resource utilization
- **Coroutine Performance**: Asynchronous I/O operations without blocking
- **Memory Efficiency**: Shared memory tables for ultra-fast data access

## ⚠️ Important Considerations

### Stateful Application Awareness

Unlike traditional PHP, Octane maintains state between requests. Be aware of:

- **Static variables persist** across requests
- **Singleton services** need careful request-specific data handling
- **Memory leaks** can accumulate over time

### Best Practices Implemented

1. **Use `--max-requests`** to restart workers periodically
2. **Avoid static variable accumulation**
3. **Inject request-specific data into methods, not constructors**
4. **Implement proper error boundaries**
5. **Monitor memory usage in production**

## 🏗 Project Structure

```
├── app/
│   ├── Http/Controllers/Dashboard/
│   │   ├── ShowPerformanceShowcaseController.php    # Main dashboard with metrics
│   │   ├── ShowSequentialController.php             # Sequential processing demo
│   │   ├── ShowConcurrentController.php             # Concurrent processing demo
│   │   ├── ShowCachedController.php                 # Cached processing demo
│   │   ├── ShowTickCachedController.php             # Tick cache demo
│   │   └── ShowRealTimeMetricsController.php        # Live metrics dashboard
│   ├── Models/
│   │   ├── Event.php                                # Event model with query scopes
│   │   └── User.php                                 # User model
│   └── Jobs/                                        # Background job classes
├── config/
│   └── octane.php                                   # Octane configuration
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── EventSeeder.php                          # 100K test events
│       └── UserSeeder.php                           # 1K test users
├── resources/views/dashboard/
│   ├── performance-showcase.blade.php               # Main dashboard view
│   ├── real-time-metrics.blade.php                  # Metrics dashboard view
│   └── default.blade.php                            # Shared dashboard template
├── routes/
│   └── web.php                                      # All performance demo routes
├── docker-compose.yml                               # Sail + Octane configuration
└── README.md
```

## 🚀 Production Deployment

### Environment Configuration

```bash
# Production environment variables
OCTANE_SERVER=swoole
OCTANE_WORKERS=auto
OCTANE_MAX_REQUESTS=1000
OCTANE_TASK_WORKERS=auto
```

### Docker Production Setup

```bash
# Build production image
docker build -t hp-laravel-app .

# Run with production settings
docker run -p 80:80 \
  -e OCTANE_WORKERS=4 \
  -e OCTANE_MAX_REQUESTS=1000 \
  hp-laravel-app
```

## 📖 Learning Resources

### Blog Series Articles

1. **[Laravel Octane & Swoole Introduction](link-to-article-1)**
2. **[Setup & Development Environment](link-to-article-2)**
3. **[Concurrency & Asynchronous Workflows](link-to-article-3)**
4. **[Advanced Caching & Performance Monitoring](link-to-article-4)**
5. **[Production Deployment & Best Practices](link-to-article-5)**

### Official Documentation

- [Laravel Octane Documentation](https://laravel.com/docs/octane)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Swoole Documentation](https://www.swoole.co.uk/)

## 🤝 Contributing

This project serves as an educational resource. Feel free to:

1. **Fork** the repository
2. **Create** a feature branch
3. **Implement** improvements or examples
4. **Submit** a pull request

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 👨‍💻 Author

**Rama** - Full Stack Developer passionate about high-performance web applications

- Blog: [Building High-Performance Laravel Applications](https://www.qisthi.dev/laravel-octane-series)
- GitHub: [@ramaID](https://github.com/ramaID)
- Business Inquiries: [rama@qisthi.dev](mailto:rama@qisthi.dev)

---

### 🎯 Next Steps

1. **Explore the Performance Showcase**: Visit `/performance-showcase` to see all optimization levels
2. **Test each performance tier**: Experience the dramatic speed differences firsthand
3. **Read the complete blog series** for deep technical understanding
4. **Experiment with cache warming**: Test `/test-ticker` and tick cache performance
5. **Deploy to production** with the provided Docker configuration

**Ready to supercharge your Laravel applications? Experience microsecond response times! 🚀**
