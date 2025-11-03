<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Secure Storage') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body {
                    font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
                }
            </style>
        @endif
    </head>
    <body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0">
            <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
            <div class="absolute top-3/4 right-1/4 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-4000"></div>
        </div>

        <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
            <div class="max-w-6xl mx-auto text-center ">
                <!-- Hero Section -->
                <div class="mb-16">
                    <h1 class="text-6xl md:text-8xl font-bold mb-6 bg-gradient-to-r from-white via-purple-200 to-blue-200 bg-clip-text text-transparent">
                        {{ config('app.name', 'Secure Storage') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-3xl mx-auto leading-relaxed">
                        Enterprise-grade file storage with advanced security, user management, and comprehensive admin controls
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}"
                               class="group relative px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full font-semibold  shadow-2xl hover:shadow-purple-500/25 transition-all duration-300 hover:scale-105 transform overflow-hidden">
                                <span class="relative z-10">Get Started</span>
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-700 to-blue-700 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </a>
                        @endif
                        <a href="#features"
                           class="px-8 py-4 border-2 border-white/30 rounded-full font-semibold  hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Features Section -->
                <div id="features" class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                    <div class="group bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105 transform hover:shadow-2xl hover:shadow-purple-500/10">
                        <div class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">🔒</div>
                        <h3 class="text-xl font-bold mb-4 text-purple-200">Secure Storage</h3>
                        <p class="text-gray-300 leading-relaxed">Bank-level encryption with role-based access control and audit trails</p>
                    </div>

                    <div class="group bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105 transform hover:shadow-2xl hover:shadow-blue-500/10">
                        <div class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">👥</div>
                        <h3 class="text-xl font-bold mb-4 text-blue-200">User Management</h3>
                        <p class="text-gray-300 leading-relaxed">Complete admin dashboard for user lifecycle and permission management</p>
                    </div>

                    <div class="group bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105 transform hover:shadow-2xl hover:shadow-green-500/10">
                        <div class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">📊</div>
                        <h3 class="text-xl font-bold mb-4 text-green-200">Storage Analytics</h3>
                        <p class="text-gray-300 leading-relaxed">Real-time monitoring with intelligent quota management and usage insights</p>
                    </div>

                    <div class="group bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105 transform hover:shadow-2xl hover:shadow-pink-500/10">
                        <div class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">🛡️</div>
                        <h3 class="text-xl font-bold mb-4 text-pink-200">Advanced Security</h3>
                        <p class="text-gray-300 leading-relaxed">Multi-layer file validation, malware scanning, and compliance features</p>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-purple-300 mb-2">99.9%</div>
                        <div class="text-sm text-gray-400 uppercase tracking-wider">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-blue-300 mb-2">256-bit</div>
                        <div class="text-sm text-gray-400 uppercase tracking-wider">Encryption</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-green-300 mb-2">24/7</div>
                        <div class="text-sm text-gray-400 uppercase tracking-wider">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-pink-300 mb-2">∞</div>
                        <div class="text-sm text-gray-400 uppercase tracking-wider">Scalability</div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
