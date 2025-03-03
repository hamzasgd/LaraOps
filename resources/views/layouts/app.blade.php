<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: window.innerWidth >= 1024, sidebarMinimized: localStorage.getItem('sidebarMinimized') === 'true' }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Ops</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
        
        .dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        
        /* Transition effects */
        .page-transition {
            transition: all 0.3s ease-in-out;
        }
        
        /* Card hover effect */
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Progress bars */
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900 dark:text-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar backdrop -->
        <div 
            x-show="sidebarOpen" 
            x-cloak
            @click="sidebarOpen = false" 
            class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden"
        ></div>
        
        <!-- Sidebar -->
        <div 
            x-cloak
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                sidebarMinimized ? 'lg:w-20' : 'lg:w-64'
            ]" 
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-all duration-200 ease-in-out lg:relative lg:translate-x-0"
        >
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('laravelops.index') }}" class="flex items-center space-x-2" :class="{ 'justify-center': sidebarMinimized }">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-xl font-bold" x-show="!sidebarMinimized">Laravel Ops</span>
                </a>
                <div class="flex">
                    <button @click="sidebarMinimized = !sidebarMinimized; localStorage.setItem('sidebarMinimized', sidebarMinimized)" class="p-1 text-gray-500 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 hidden lg:block">
                        <svg x-show="!sidebarMinimized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        </svg>
                        <svg x-show="sidebarMinimized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <button @click="sidebarOpen = false" class="p-1 text-gray-500 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-4" :class="{ 'px-2': sidebarMinimized }">
                <!-- Search -->
                <div class="relative mb-6" x-show="!sidebarMinimized">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" class="w-full pl-10 pr-4 py-2 text-sm text-gray-900 bg-gray-100 border-0 rounded-md dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Search...">
                </div>
                
                <!-- Navigation -->
                <nav class="space-y-1">
                    <a href="{{ route('laravelops.system.index') }}" 
                       class="{{ request()->routeIs('laravelops.system.*') ? 'bg-primary-50 text-primary-700 dark:bg-gray-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md relative"
                       :class="{ 'justify-center': sidebarMinimized }">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('laravelops.system.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}" 
                             :class="{ 'mr-0': sidebarMinimized }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span x-show="!sidebarMinimized">Dashboard</span>
                        <div x-show="sidebarMinimized" class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
                            Dashboard
                        </div>
                    </a>
                    
                    <a href="{{ route('laravelops.logs.index') }}" 
                       class="{{ request()->routeIs('laravelops.logs.*') ? 'bg-primary-50 text-primary-700 dark:bg-gray-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md relative"
                       :class="{ 'justify-center': sidebarMinimized }">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('laravelops.logs.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}" 
                             :class="{ 'mr-0': sidebarMinimized }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span x-show="!sidebarMinimized">Logs</span>
                        <div x-show="sidebarMinimized" class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
                            Logs
                        </div>
                    </a>
                    
                    <a href="{{ route('laravelops.artisan.index') }}" 
                       class="{{ request()->routeIs('laravelops.artisan.*') ? 'bg-primary-50 text-primary-700 dark:bg-gray-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md relative"
                       :class="{ 'justify-center': sidebarMinimized }">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('laravelops.artisan.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}" 
                             :class="{ 'mr-0': sidebarMinimized }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="!sidebarMinimized">Artisan</span>
                        <div x-show="sidebarMinimized" class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
                            Artisan
                        </div>
                    </a>
                    
                    <a href="{{ route('laravelops.env.index') }}" 
                       class="{{ request()->routeIs('laravelops.env.*') ? 'bg-primary-50 text-primary-700 dark:bg-gray-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md relative"
                       :class="{ 'justify-center': sidebarMinimized }">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('laravelops.env.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}" 
                             :class="{ 'mr-0': sidebarMinimized }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 118 0 3 3 0 01-6 0z"></path>
                        </svg>
                        <span x-show="!sidebarMinimized">Environment</span>
                        <div x-show="sidebarMinimized" class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
                            Environment
                        </div>
                    </a>
                    
                    <a href="{{ route('laravelops.tinker.index') }}" 
                       class="{{ request()->routeIs('laravelops.tinker.*') ? 'bg-primary-50 text-primary-700 dark:bg-gray-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md relative"
                       :class="{ 'justify-center': sidebarMinimized }">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('laravelops.tinker.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}" 
                             :class="{ 'mr-0': sidebarMinimized }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span x-show="!sidebarMinimized">Tinker</span>
                        <div x-show="sidebarMinimized" class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
                            Tinker
                        </div>
                    </a>
                </nav>
            </div>
            
            <!-- Bottom section -->
            <div class="absolute bottom-0 w-full p-4 border-t border-gray-200 dark:border-gray-700" :class="{ 'flex justify-center': sidebarMinimized }">
                <div class="flex items-center justify-between" :class="{ 'flex-col space-y-2': sidebarMinimized }">
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 text-gray-500 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </button>
                    
                    <div class="text-xs text-gray-500 dark:text-gray-400" x-show="!sidebarMinimized">
                        Laravel Ops v1.0
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navbar -->
            <div class="bg-white dark:bg-gray-800 shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-1 text-gray-500 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <div class="flex items-center space-x-4">
                        <a href="https://github.com/hamzasgd/laravelops" target="_blank" class="p-1 text-gray-500 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>