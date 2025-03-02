@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Log File: {{ $filename }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('laravelops.logs.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Files
            </a>
            <a href="{{ route('laravelops.logs.live', ['file' => $filename]) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded inline-flex items-center text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Live View
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium">{{ $filename }}</span>
            </div>
            <div>
                <input type="text" id="searchInput" placeholder="Search logs..." class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400">
            </div>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                        Level
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Message
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="logEntries">
                @forelse($logs as $index => $log)
                    <tr class="log-entry cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ strtolower($log['level']) == 'error' ? 'bg-red-50 dark:bg-red-900/20' : '' }}" 
                        data-bs-toggle="collapse" data-bs-target="#log-{{ $index }}" aria-expanded="false">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ strtolower($log['level']) == 'error' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 
                                  (strtolower($log['level']) == 'info' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                {{ $log['level'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex justify-between items-center">
                                <span class="truncate max-w-lg">
                                    {{ $log['message'] }}
                                    @if(isset($log['exceptionData']['class']))
                                    <span class="text-xs text-red-500 dark:text-red-400 ml-2">({{ $log['exceptionData']['class'] }})</span>
                                    @endif
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $log['datetime'] }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="hidden" id="log-{{ $index }}">
                        <td colspan="2" class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50">
                            <!-- Error Message Section -->
                            <div class="text-sm text-gray-800 dark:text-gray-200 break-words whitespace-pre-wrap"><strong>Message:</strong> {{ $log['message'] }}</div>
                            
                            <!-- Debug info -->
                            <div class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Debug Info:</h4>
                                <pre class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded overflow-x-auto break-words whitespace-pre-wrap">Has Exception Data: {{ isset($log['exceptionData']) && !empty($log['exceptionData']) ? 'Yes' : 'No' }}
Exception Keys: {{ isset($log['exceptionData']) ? implode(', ', array_keys($log['exceptionData'])) : 'None' }}
Has Stack Trace: {{ isset($log['stackTrace']) && !empty($log['stackTrace']) ? 'Yes' : 'No' }}
Stack Trace Length: {{ isset($log['stackTrace']) ? strlen($log['stackTrace']) : '0' }}</pre>
                            </div>
                            
                            <!-- Exception Details Section -->
                            @if(isset($log['exceptionData']) && !empty($log['exceptionData']))
                            <div class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                                @if(isset($log['exceptionData']['is_data']) && $log['exceptionData']['is_data'])
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">JSON Data:</h4>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded overflow-x-auto break-words">
                                        @if(isset($log['exceptionData']['data']) && is_array($log['exceptionData']['data']))
                                            @foreach($log['exceptionData']['data'] as $key => $value)
                                                <div><strong>{{ ucfirst($key) }}:</strong> {{ is_scalar($value) ? $value : json_encode($value) }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                @else
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exception Details:</h4>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded overflow-x-auto break-words">
                                        @if(isset($log['exceptionData']['class']))<div><strong>Type:</strong> {{ $log['exceptionData']['class'] }}</div>@endif
                                        @if(isset($log['exceptionData']['code']))<div><strong>Code:</strong> {{ $log['exceptionData']['code'] }}</div>@endif
                                        @if(isset($log['exceptionData']['message']))<div><strong>Message:</strong> {{ $log['exceptionData']['message'] }}</div>@endif
                                        @if(isset($log['exceptionData']['file']))<div><strong>File:</strong> {{ $log['exceptionData']['file'] }}</div>@endif
                                        @if(isset($log['exceptionData']['line']))<div><strong>Line:</strong> {{ $log['exceptionData']['line'] }}</div>@endif
                                        @if(isset($log['exceptionData']['exception']))<div><strong>Raw Exception:</strong> {{ $log['exceptionData']['exception'] }}</div>@endif
                                    </div>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Stack Trace Section -->
                            @if(isset($log['stackTrace']) && $log['stackTrace'])
                                <div class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stack Trace:</h4>
                                    <pre class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded overflow-x-auto break-words whitespace-pre-wrap">{!! $formatStackTrace($log['stackTrace']) !!}</pre>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No log entries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection 