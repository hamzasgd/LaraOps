<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
            Laravel Information
        </h2>
    </div>
    <div class="p-5">
        @if(isset($laravelInfo) && is_array($laravelInfo) && count($laravelInfo) > 0)
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Laravel Version</div>
                    <div class="font-medium text-gray-900 dark:text-white">{{ $laravelInfo['Laravel Version'] ?? 'Unknown' }}</div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($laravelInfo as $key => $value)
                    @if($key !== 'Laravel Version')
                        <div class="px-4 py-3 flex justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $key }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $value }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @else
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">No Laravel information available</p>
        </div>
        @endif
        
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Configuration</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 {{ config('app.debug') ? 'text-yellow-500' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Debug Mode</span>
                    </div>
                    <div class="ml-7 text-sm {{ config('app.debug') ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Environment</span>
                    </div>
                    <div class="ml-7 text-sm text-blue-600 dark:text-blue-400">
                        {{ app()->environment() }}
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</span>
                    </div>
                    <div class="ml-7 text-sm text-purple-600 dark:text-purple-400">
                        {{ config('app.timezone') }}
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cache Driver</span>
                    </div>
                    <div class="ml-7 text-sm text-indigo-600 dark:text-indigo-400">
                        {{ config('cache.default') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 