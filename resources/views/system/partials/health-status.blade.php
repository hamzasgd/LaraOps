<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 {{ $criticalIssues > 0 ? 'text-red-500' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            System Health
            @if($criticalIssues > 0)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                    {{ $criticalIssues }} {{ Str::plural('issue', $criticalIssues) }}
                </span>
            @else
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                    All systems operational
                </span>
            @endif
        </h2>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($healthChecks as $check)
                <div class="relative group">
                    <div class="border rounded-lg p-4 flex items-start transition-all duration-200
                        {{ $check['status'] === 'critical' ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20' :
                           ($check['status'] === 'warning' ? 'border-yellow-300 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/20' : 
                           'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20') }}">
                        <div class="mr-4 flex-shrink-0">
                            @if($check['status'] === 'critical')
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @elseif($check['status'] === 'warning')
                                <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium
                                {{ $check['status'] === 'critical' ? 'text-red-800 dark:text-red-300' :
                                   ($check['status'] === 'warning' ? 'text-yellow-800 dark:text-yellow-300' : 
                                   'text-green-800 dark:text-green-300') }}">
                                {{ $check['name'] }}
                            </h3>
                            <p class="mt-1 text-sm
                                {{ $check['status'] === 'critical' ? 'text-red-700 dark:text-red-200' :
                                   ($check['status'] === 'warning' ? 'text-yellow-700 dark:text-yellow-200' : 
                                   'text-green-700 dark:text-green-200') }}">
                                {{ $check['message'] }}
                            </p>
                            
                            @if($check['status'] === 'critical')
                                <div class="mt-3">
                                    <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-red-300 dark:border-red-700 shadow-sm text-xs font-medium rounded text-red-700 dark:text-red-200 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-900 transition-colors duration-150">
                                        Fix issue
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Tooltip -->
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200">
                            <div class="absolute top-0 right-0 mt-2 mr-2">
                                <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs rounded py-1 px-2 shadow-lg">
                                    Click for details
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <span class="text-sm text-gray-500 dark:text-gray-400">Last checked: {{ now()->format('M j, Y g:i A') }}</span>
        <form action="{{ route('laravelops.system.index') }}" method="GET">
            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Run Health Check
            </button>
        </form>
    </div>
</div>
