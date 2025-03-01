<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            System Information
        </h2>
    </div>
    <div class="p-5">
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Operating System</div>
                    <div class="font-medium text-gray-900 dark:text-white">{{ $systemInfo['Operating System'] ?? 'Unknown' }}</div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($systemInfo as $key => $value)
                    @if($key !== 'Operating System')
                        <div class="px-4 py-3 flex justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $key }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $value }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        @if(isset($phpExtensions) && count($phpExtensions) > 0)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">PHP Extensions</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                @foreach($phpExtensions as $extension)
                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700/50 rounded text-sm text-gray-700 dark:text-gray-300 truncate">
                        {{ $extension }}
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">PHP Extensions</h3>
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">No PHP extension information available</p>
            </div>
        </div>
        @endif
    </div>
</div> 