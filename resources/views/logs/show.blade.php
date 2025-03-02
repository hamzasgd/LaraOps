@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            <svg class="w-7 h-7 mr-2 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="truncate">{{ $filename }}</span>
        </h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('laravelops.logs.index') }}" 
               class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150 shadow-sm">
                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Files
            </a>
            <a href="{{ route('laravelops.logs.live', ['file' => $filename]) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2.5 inline-flex items-center text-sm font-medium transition-colors duration-150 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Live View
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 transition-all duration-200">
        <div class="border-b border-gray-200 dark:border-gray-700 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $filename }}</span>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search logs..." class="w-full md:w-64 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors duration-150">
            </div>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                        Level
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Message
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-40">
                        Timestamp
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="logEntries">
                @forelse($logs as $index => $log)
                    <tr class="log-entry cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 {{ strtolower($log['level']) == 'error' ? 'bg-red-50 dark:bg-red-900/20' : (strtolower($log['level']) == 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '') }}" 
                        data-bs-toggle="collapse" data-bs-target="#log-{{ $index }}" aria-expanded="false">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full 
                                {{ strtolower($log['level']) == 'error' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 
                                  (strtolower($log['level']) == 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : 
                                  (strtolower($log['level']) == 'info' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 
                                  (strtolower($log['level']) == 'debug' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300' :
                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))) }}">
                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    @if(strtolower($log['level']) == 'error')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @elseif(strtolower($log['level']) == 'warning')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    @elseif(strtolower($log['level']) == 'info')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @elseif(strtolower($log['level']) == 'debug')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @endif
                                </svg>
                                {{ $log['level'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex justify-between items-center">
                                <span class="font-medium truncate max-w-lg">
                                    {{ preg_replace('/\s+/', ' ', trim($log['message'])) }}
                                    @if(isset($log['exceptionData']['class']))
                                    <span class="text-xs text-red-500 dark:text-red-400 ml-2 font-normal">({{ $log['exceptionData']['class'] }})</span>
                                    @endif
                                </span>
                                <svg class="w-4 h-4 ml-2 text-gray-400 dark:text-gray-500 flex-shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right whitespace-nowrap">
                            <span class="bg-gray-100 dark:bg-gray-700 px-2.5 py-1.5 rounded-md text-xs">
                                {{ $log['datetime'] }}
                            </span>
                        </td>
                    </tr>
                    <tr class="hidden" id="log-{{ $index }}">
                        <td colspan="3" class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50">
                            <!-- Error Message Section -->
                            <div class="text-sm text-gray-800 dark:text-gray-200 break-words whitespace-normal mb-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-600 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Message</span>
                                </div>
                                <div class="bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-lg shadow-sm">
                                    <div class="py-3 px-4">{{ $log['message'] }}</div>
                                </div>
                            </div>
                            
                            <!-- Debug info -->
                            <div class="mt-5 border-t border-gray-200 dark:border-gray-600 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Debug Info
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow-inner">
                                    <div class="flex items-center p-3 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 shadow-sm">
                                        <div class="mr-3 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 p-2 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Exception Data</span>
                                            <span>{{ isset($log['exceptionData']) && !empty($log['exceptionData']) ? 'Yes' : 'No' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-3 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 shadow-sm">
                                        <div class="mr-3 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 p-2 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Exception Keys</span>
                                            <span class="break-words">{{ isset($log['exceptionData']) ? implode(', ', array_keys($log['exceptionData'])) : 'None' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-3 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 shadow-sm">
                                        <div class="mr-3 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 p-2 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Stack Trace</span>
                                            <span>{{ isset($log['stackTrace']) && !empty($log['stackTrace']) ? 'Yes' : 'No' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-3 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 shadow-sm">
                                        <div class="mr-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 p-2 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Stack Trace Length</span>
                                            <span>{{ isset($log['stackTrace']) ? strlen($log['stackTrace']) . ' chars' : '0 chars' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Data or Exception Details Section -->
                            @if(isset($log['exceptionData']) && !empty($log['exceptionData']))
                            <div class="mt-5 border-t border-gray-200 dark:border-gray-600 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                    @if(isset($log['exceptionData']['is_data']) && $log['exceptionData']['is_data'])
                                        <svg class="w-4 h-4 mr-1.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Data Details
                                    @else
                                        <svg class="w-4 h-4 mr-1.5 text-red-500 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Exception Details
                                    @endif
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow-inner">
                                    @if(isset($log['exceptionData']['is_data']) && $log['exceptionData']['is_data'] && isset($log['exceptionData']['data']))
                                        <!-- Display data in a formatted way -->
                                        <div class="p-3 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650 shadow-sm md:col-span-2">
                                            <span class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Data</span>
                                            <pre class="text-gray-600 dark:text-gray-400 overflow-x-auto whitespace-pre-wrap bg-gray-50 dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700">{{ json_encode($log['exceptionData']['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </div>
                                    @else
                                        <!-- Display exception details -->
                                        @foreach($log['exceptionData'] as $key => $value)
                                            @if($key != 'stackTrace' && $key != 'is_data')
                                                <div class="p-3 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650 shadow-sm {{ in_array($key, ['message', 'file']) ? 'md:col-span-2' : '' }}">
                                                    <span class="block font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($key) }}</span>
                                                    <span class="text-gray-600 dark:text-gray-400 break-words">
                                                        @if(is_array($value) || (is_string($value) && (substr($value, 0, 1) == '{' || substr($value, 0, 1) == '[')))
                                                            @php
                                                                if(is_string($value)) {
                                                                    try {
                                                                        $jsonValue = json_decode($value, true);
                                                                        if(json_last_error() === JSON_ERROR_NONE) {
                                                                            $value = $jsonValue;
                                                                        }
                                                                    } catch(\Exception $e) {
                                                                        // Keep original value if JSON parsing fails
                                                                    }
                                                                }
                                                            @endphp
                                                            <pre class="bg-gray-50 dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-700 overflow-x-auto">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value }}</pre>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Stack Trace Section -->
                            @if(isset($log['stackTrace']) && !empty($log['stackTrace']))
                            <div class="mt-5 border-t border-gray-200 dark:border-gray-600 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-indigo-500 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Stack Trace
                                </h4>
                                <div class="bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-lg shadow-sm max-h-60 overflow-y-auto">
                                    <pre class="text-xs text-gray-600 dark:text-gray-400 p-4 overflow-x-auto break-words whitespace-pre-wrap">{{ $log['stackTrace'] }}</pre>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No log entries found</p>
                            <p class="mt-1">Try changing your search criteria or check back later.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <nav aria-label="Page navigation">
            <ul class="flex justify-center">
                @if($currentPage > 1)
                    <li>
                        <a href="{{ route('laravelops.logs.show', ['filename' => $filename, 'page' => $currentPage - 1, 'perPage' => $perPage]) }}" class="px-4 py-2 border border-gray-300 rounded-lg">Previous</a>
                    </li>
                @endif
                @for($i = 1; $i <= $lastPage; $i++)
                    <li>
                        <a href="{{ route('laravelops.logs.show', ['filename' => $filename, 'page' => $i, 'perPage' => $perPage]) }}" class="px-4 py-2 border border-gray-300 rounded-lg {{ $currentPage == $i ? 'bg-indigo-600 text-white' : '' }}">{{ $i }}</a>
                    </li>
                @endfor
                @if($currentPage < $lastPage)
                    <li>
                        <a href="{{ route('laravelops.logs.show', ['filename' => $filename, 'page' => $currentPage + 1, 'perPage' => $perPage]) }}" class="px-4 py-2 border border-gray-300 rounded-lg">Next</a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>

<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                // Get all log entries
                const logEntries = document.querySelectorAll('#logEntries tr.log-entry');
                
                // If search term is empty, show all entries
                if (searchTerm === '') {
                    document.querySelectorAll('#logEntries tr').forEach(row => {
                        row.style.display = '';
                    });
                    return;
                }
                
                // Process each log entry
                logEntries.forEach(entry => {
                    // Get the detail row ID from the data-bs-target attribute
                    const detailRowId = entry.getAttribute('data-bs-target').substring(1);
                    const detailRow = document.getElementById(detailRowId);
                    
                    // Get all text content from both rows
                    const entryText = entry.textContent.toLowerCase();
                    const detailText = detailRow ? detailRow.textContent.toLowerCase() : '';
                    
                    // Check if either row contains the search term
                    const isVisible = entryText.includes(searchTerm) || detailText.includes(searchTerm);
                    
                    // Show/hide both rows based on search result
                    entry.style.display = isVisible ? '' : 'none';
                    if (detailRow) {
                        detailRow.style.display = isVisible ? (detailRow.classList.contains('hidden') ? 'none' : '') : 'none';
                    }
                });
            });
        }
        
        // Toggle detail rows
        const logEntries = document.querySelectorAll('#logEntries tr.log-entry');
        logEntries.forEach(entry => {
            entry.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target').substring(1);
                const detailRow = document.getElementById(targetId);
                
                if (detailRow) {
                    const isHidden = detailRow.classList.contains('hidden');
                    detailRow.classList.toggle('hidden');
                    detailRow.style.display = isHidden ? '' : 'none';
                    
                    // Rotate the arrow icon
                    const arrow = this.querySelector('svg.transition-transform');
                    if (arrow) {
                        arrow.style.transform = isHidden ? 'rotate(180deg)' : '';
                    }
                }
            });
        });
    });
</script>
@endsection 