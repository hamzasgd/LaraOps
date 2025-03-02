@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-7 h-7 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Live Log Viewer
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Real-time log monitoring</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <div class="relative">
                <button id="logFileDropdown" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-150 shadow-sm">
                    <svg class="mr-2 h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>{{ $currentLog }}</span>
                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div id="logFileMenu" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10 border border-gray-100 dark:border-gray-600 transition-all duration-200">
                    <div class="py-1 max-h-60 overflow-y-auto" role="menu" aria-orientation="vertical">
                        @foreach($files as $file)
                            <a href="{{ route('laravelops.logs.live', ['file' => $file['name']]) }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150" role="menuitem">
                                {{ $file['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <a href="{{ route('laravelops.logs.index') }}" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-150 shadow-sm">
                <svg class="mr-2 h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                All Logs
            </a>
            <button id="refreshBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2.5 inline-flex items-center text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-150 shadow-sm hover:shadow-md">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 transition-all duration-200">
        <div class="border-b border-gray-200 dark:border-gray-700 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $currentLog }}</span>
            </div>
            <div class="w-full md:w-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search logs..." class="w-full md:w-64 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors duration-150">
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                            Level
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Message
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="logEntries">
                    @forelse($logs as $index => $log)
                        <tr class="log-entry cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 {{ strtolower($log['level']) == 'error' ? 'bg-red-50 dark:bg-red-900/20' : (strtolower($log['level']) == 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '') }}" 
                            data-bs-toggle="collapse" data-bs-target="#log-{{ $index }}" aria-expanded="false">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ strtolower($log['level']) == 'error' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 
                                      (strtolower($log['level']) == 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : 
                                       (strtolower($log['level']) == 'info' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) }}">
                                    <svg class="inline-block w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        @if(strtolower($log['level']) == 'error')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @elseif(strtolower($log['level']) == 'warning')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        @elseif(strtolower($log['level']) == 'info')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @endif
                                    </svg>
                                    {{ $log['level'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex justify-between items-center">
                                    <span class="truncate max-w-lg">{{ $log['message'] }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap ml-2 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md">{{ $log['datetime'] }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr class="hidden" id="log-{{ $index }}">
                            <td colspan="2" class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50">
                                <div class="text-sm text-gray-800 dark:text-gray-200 break-words whitespace-pre-wrap">{{ $log['message'] }}</div>
                                
                                <!-- Debug info -->
                                <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Debug Info:
                                    </h4>
                                    <pre class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded-lg overflow-x-auto break-words whitespace-pre-wrap">
Has Exception Data: {{ isset($log['exceptionData']) && !empty($log['exceptionData']) ? 'Yes' : 'No' }}
Exception Keys: {{ isset($log['exceptionData']) ? implode(', ', array_keys($log['exceptionData'])) : 'None' }}
Has Stack Trace: {{ isset($log['stackTrace']) && !empty($log['stackTrace']) ? 'Yes' : 'No' }}
Stack Trace Length: {{ isset($log['stackTrace']) ? strlen($log['stackTrace']) : '0' }}
                                    </pre>
                                </div>
                                
                                @if(isset($log['exceptionData']) && !empty($log['exceptionData']))
                                    <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Exception Details:
                                        </h4>
                                        <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded-lg overflow-x-auto break-words whitespace-pre-wrap">
                                            @foreach($log['exceptionData'] as $key => $value)
                                                @if(!is_array($value))
                                                    <div class="mb-1"><strong class="text-gray-700 dark:text-gray-300">{{ $key }}:</strong> {{ $value }}</div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @if($log['stackTrace'])
                                    <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            Stack Trace:
                                        </h4>
                                        <pre class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded-lg overflow-x-auto break-words whitespace-pre-wrap">{!! $formatStackTrace($log['stackTrace']) !!}</pre>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center py-6">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    No log entries found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dropdown
    const logFileDropdown = document.getElementById('logFileDropdown');
    const logFileMenu = document.getElementById('logFileMenu');
    
    if (logFileDropdown && logFileMenu) {
        logFileDropdown.addEventListener('click', function() {
            logFileMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!logFileDropdown.contains(event.target)) {
                logFileMenu.classList.add('hidden');
            }
        });
    }
    
    // Toggle log details
    const logEntries = document.querySelectorAll('.log-entry');
    logEntries.forEach(entry => {
        entry.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.classList.toggle('hidden');
            }
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const logEntries = document.querySelectorAll('#logEntries tr.log-entry');
            
            logEntries.forEach(entry => {
                const nextRow = entry.nextElementSibling;
                const text = entry.textContent.toLowerCase();
                const isVisible = text.includes(searchTerm);
                
                entry.style.display = isVisible ? '' : 'none';
                if (nextRow && nextRow.id) {
                    nextRow.style.display = 'none'; // Always hide detail rows when searching
                }
            });
        });
    }
    
    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            window.location.reload();
        });
    }
});
</script>
@endsection 