#!/bin/bash

# Setup script for Laravel Octane with Nginx local development environment
# This script helps set up the complete production-like development environment

echo "🚀 Setting up Laravel Octane with Nginx local development environment..."

# Check if Docker and Docker Compose are installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create necessary directories
echo "📁 Creating Nginx configuration directories..."
mkdir -p docker/nginx/sites
mkdir -p docker/nginx/ssl

# Check if Laravel Octane is installed
if ! grep -q "laravel/octane" composer.json; then
    echo "📦 Installing Laravel Octane..."
    ./vendor/bin/sail composer require laravel/octane
    ./vendor/bin/sail artisan octane:install --server=swoole
fi

# Backup existing docker-compose.yml if it exists
if [ -f "docker-compose.yml" ]; then
    echo "💾 Backing up existing docker-compose.yml..."
    cp docker-compose.yml docker-compose.yml.backup
fi

# Copy the enhanced docker-compose.yml
echo "📋 Setting up enhanced Docker Compose configuration..."
cp docker-compose.enhanced.yml docker-compose.yml

# Check if configuration files exist
if [ ! -f "docker/nginx/nginx.conf" ]; then
    echo "❌ Nginx configuration files not found!"
    echo "Please ensure docker/nginx/nginx.conf and docker/nginx/sites/laravel.conf exist."
    exit 1
fi

# Start the services
echo "🐳 Starting services..."
./vendor/bin/sail down
./vendor/bin/sail up -d

# Wait a moment for services to start
echo "⏳ Waiting for services to start..."
sleep 10

# Test the setup
echo "🧪 Testing the setup..."

# Check if Nginx is responding
if curl -s -I http://localhost | grep -q "nginx"; then
    echo "✅ Nginx is running and responding!"
else
    echo "❌ Nginx test failed. Check the logs with: ./vendor/bin/sail logs nginx"
fi

# Check if Octane is responding
if curl -s http://localhost/health > /dev/null 2>&1; then
    echo "✅ Laravel Octane is running and responding!"
else
    echo "❌ Octane test failed. Check the logs with: ./vendor/bin/sail logs laravel.test"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "You can now:"
echo "  • Access your app at: http://localhost"
echo "  • Monitor Nginx logs: docker logs -f laravel_nginx"
echo "  • Monitor Octane logs: ./vendor/bin/sail logs -f laravel.test"
echo "  • Test static files: curl -I http://localhost/favicon.ico"
echo "  • Test health check: curl http://localhost/health"
echo ""
echo "To stop all services: ./vendor/bin/sail down"
