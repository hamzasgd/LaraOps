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
            <div class="w-full md:w-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search logs..." class="w-full md:w-64 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors duration-150">
                </div>
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
                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) }}">
                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
                                <span class="truncate max-w-lg">
                                    {{ preg_replace('/\s+/', ' ', trim($log['message'])) }}
                                    @if(isset($log['exceptionData']['class']))
                                    <span class="text-xs text-red-500 dark:text-red-400 ml-2">({{ $log['exceptionData']['class'] }})</span>
                                    @endif
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap ml-2 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md">{{ $log['datetime'] }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="hidden" id="log-{{ $index }}">
                        <td colspan="2" class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50">
                            <!-- Error Message Section -->
                            <div class="text-sm text-gray-800 dark:text-gray-200 break-words whitespace-normal mb-2">
                                <div class="flex items-center mb-1">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-600 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Message</span>
                                </div>
                                <div class="bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-lg">
                                    <div class="py-2 px-3">{{ $log['message'] }}</div>
                                </div>
                            </div>
                            
                            <!-- Debug info -->
                            <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Debug Info
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
                                    <div class="flex items-center p-2 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650">
                                        <div class="mr-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 p-1.5 rounded">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Exception Data</span>
                                            <span>{{ isset($log['exceptionData']) && !empty($log['exceptionData']) ? 'Yes' : 'No' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-2 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650">
                                        <div class="mr-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 p-1.5 rounded">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Exception Keys</span>
                                            <span class="break-words">{{ isset($log['exceptionData']) ? implode(', ', array_keys($log['exceptionData'])) : 'None' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-2 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650">
                                        <div class="mr-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 p-1.5 rounded">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Stack Trace</span>
                                            <span>{{ isset($log['stackTrace']) && !empty($log['stackTrace']) ? 'Yes' : 'No' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-2 rounded bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650">
                                        <div class="mr-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 p-1.5 rounded">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block font-medium text-gray-700 dark:text-gray-300">Stack Trace Length</span>
                                            <span>{{ isset($log['stackTrace']) ? strlen($log['stackTrace']) : '0' }} chars</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Exception Details Section -->
                            @if(isset($log['exceptionData']) && !empty($log['exceptionData']))
                            <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                @if(isset($log['exceptionData']['is_data']) && $log['exceptionData']['is_data'])
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            JSON Data
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button type="button" class="view-toggle grid-view-btn bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 text-xs rounded-md font-medium active" data-target="grid-view-{{ $index }}" data-alternate="json-view-{{ $index }}">
                                                <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                                </svg>
                                                Grid
                                            </button>
                                            <button type="button" class="view-toggle json-view-btn bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 text-xs rounded-md font-medium" data-target="json-view-{{ $index }}" data-alternate="grid-view-{{ $index }}">
                                                <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                                </svg>
                                                JSON
                                            </button>
                                        </div>
                                    </h4>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 p-3 rounded-lg overflow-x-auto break-words">
                                        @if(isset($log['exceptionData']['data']) && is_array($log['exceptionData']['data']))
                                            <div id="grid-view-{{ $index }}" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            @foreach($log['exceptionData']['data'] as $key => $value)
                                                <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650">
                                                    <span class="block font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($key) }}</span>
                                                    <span class="text-gray-600 dark:text-gray-400">{{ is_scalar($value) ? $value : json_encode($value) }}</span>
                                                </div>
                                            @endforeach
                                            </div>
                                            <div id="json-view-{{ $index }}" class="hidden">
                                                <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">JSON Data</span>
                                                        <button type="button" class="copy-json-btn bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 px-2 py-1 text-xs rounded-md font-medium flex items-center transition-colors duration-150" data-json="{{ htmlspecialchars(json_encode($log['exceptionData']['data'])) }}">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                                            </svg>
                                                            Copy JSON
                                                        </button>
                                                    </div>
                                                    <pre class="text-gray-600 dark:text-gray-400 overflow-x-auto">{{ json_encode($log['exceptionData']['data'], JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-red-500 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Exception Details
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
                                        @if(isset($log['exceptionData']['class']))
                                            <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650">
                                                <span class="block font-medium text-gray-700 dark:text-gray-300">Type</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ $log['exceptionData']['class'] }}</span>
                                            </div>
                                        @endif
                                        @if(isset($log['exceptionData']['code']))
                                            <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650">
                                                <span class="block font-medium text-gray-700 dark:text-gray-300">Code</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ $log['exceptionData']['code'] }}</span>
                                            </div>
                                        @endif
                                        @if(isset($log['exceptionData']['message']))
                                            <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650 md:col-span-2">
                                                <span class="block font-medium text-gray-700 dark:text-gray-300">Message</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ $log['exceptionData']['message'] }}</span>
                                            </div>
                                        @endif
                                        @if(isset($log['exceptionData']['file']))
                                            <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650 md:col-span-2">
                                                <span class="block font-medium text-gray-700 dark:text-gray-300">File</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ $log['exceptionData']['file'] }}</span>
                                            </div>
                                        @endif
                                        @if(isset($log['exceptionData']['line']))
                                            <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650">
                                                <span class="block font-medium text-gray-700 dark:text-gray-300">Line</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ $log['exceptionData']['line'] }}</span>
                                            </div>
                                        @endif
                                        @if(isset($log['exceptionData']['exception']))
                                            <div class="p-2 bg-white dark:bg-gray-750 rounded border border-gray-200 dark:border-gray-650 md:col-span-2">
                                                <span class="block font-medium text-gray-700 dark:text-gray-300">Raw Exception</span>
                                                <span class="text-gray-600 dark:text-gray-400 break-words">{{ $log['exceptionData']['exception'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Stack Trace Section -->
                            @if(isset($log['stackTrace']) && $log['stackTrace'])
                                <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-indigo-500 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Stack Trace
                                    </h4>
                                    <div class="bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-lg p-1">
                                        <pre class="text-xs text-gray-600 dark:text-gray-400 p-2 overflow-x-auto break-words whitespace-pre-wrap">{!! $formatStackTrace($log['stackTrace']) !!}</pre>
                                    </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // If search term is empty, show all entries
            if (searchTerm === '') {
                document.querySelectorAll('#logEntries tr').forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            // Process each log entry pair (main row + detail row)
            const logEntries = document.querySelectorAll('#logEntries tr.log-entry');
            
            logEntries.forEach(entry => {
                // Get the detail row
                const detailRow = document.getElementById(entry.getAttribute('data-bs-target').substring(1));
                
                // Get all text content from both rows
                const entryText = entry.textContent.toLowerCase();
                const detailText = detailRow ? detailRow.textContent.toLowerCase() : '';
                
                // Check if either row contains the search term
                const isVisible = entryText.includes(searchTerm) || detailText.includes(searchTerm);
                
                // Show/hide both rows based on search result
                entry.style.display = isVisible ? '' : 'none';
                if (detailRow) {
                    detailRow.style.display = isVisible ? '' : 'none';
                }
            });
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
                // Ensure the detail row is displayed if the entry is expanded
                targetElement.style.display = targetElement.classList.contains('hidden') ? 'none' : '';
            }
        });
    });
    
    // View toggle functionality
    const viewToggleButtons = document.querySelectorAll('.view-toggle');
    viewToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const alternateId = this.getAttribute('data-alternate');
            const targetElement = document.getElementById(targetId);
            const alternateElement = document.getElementById(alternateId);
            
            // Show the target view and hide the alternate view
            if (targetElement && alternateElement) {
                targetElement.classList.remove('hidden');
                alternateElement.classList.add('hidden');
                
                // Update button styles
                document.querySelector(`[data-target="${targetId}"]`).classList.add('active', 'bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-700', 'dark:text-blue-300');
                document.querySelector(`[data-target="${targetId}"]`).classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
                
                document.querySelector(`[data-target="${alternateId}"]`).classList.remove('active', 'bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-700', 'dark:text-blue-300');
                document.querySelector(`[data-target="${alternateId}"]`).classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            }
        });
    });
    
    // Copy JSON to clipboard functionality
    const copyButtons = document.querySelectorAll('.copy-json-btn');
    copyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            
            try {
                const jsonData = this.getAttribute('data-json');
                
                // Get the pre-formatted JSON directly from the pre element
                const preElement = this.closest('.p-2').querySelector('pre');
                const jsonText = preElement ? preElement.textContent : jsonData;
                
                // Use the modern clipboard API if available
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(jsonText).then(() => {
                        showCopySuccess(this);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                        fallbackCopyTextToClipboard(jsonText, this, false);
                    });
                } else {
                    fallbackCopyTextToClipboard(jsonText, this, false);
                }
            } catch (error) {
                console.error('Error copying JSON: ', error);
                // Try to copy the raw text as a fallback
                try {
                    const preElement = this.closest('.p-2').querySelector('pre');
                    if (preElement) {
                        const rawText = preElement.textContent;
                        fallbackCopyTextToClipboard(rawText, this, false);
                    }
                } catch (e) {
                    console.error('Failed to copy raw text: ', e);
                }
            }
        });
    });
    
    // Fallback copy method for older browsers
    function fallbackCopyTextToClipboard(text, button, shouldParse = true) {
        try {
            // Create a temporary textarea element to copy from
            const textarea = document.createElement('textarea');
            textarea.value = shouldParse ? JSON.stringify(JSON.parse(text), null, 2) : text; // Format the JSON nicely if needed
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            
            // Select and copy the text
            textarea.select();
            document.execCommand('copy');
            
            // Remove the temporary element
            document.body.removeChild(textarea);
            
            // Show success message
            showCopySuccess(button);
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err);
        }
    }
    
    // Show copy success UI feedback
    function showCopySuccess(button) {
        // Update button text temporarily to show success
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="w-3.5 h-3.5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Copied!
        `;
        button.classList.remove('bg-gray-100', 'hover:bg-gray-200', 'dark:bg-gray-700', 'dark:hover:bg-gray-600');
        button.classList.add('bg-green-100', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-300');
        
        // Reset button after 2 seconds
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.add('bg-gray-100', 'hover:bg-gray-200', 'dark:bg-gray-700', 'dark:hover:bg-gray-600');
            button.classList.remove('bg-green-100', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-300');
        }, 2000);
    }
    
    // Syntax highlighting for stack traces
    document.querySelectorAll('pre').forEach(block => {
        highlightStackTrace(block);
    });
    
    function highlightStackTrace(element) {
        const html = element.innerHTML;
        
        // Highlight file paths
        const highlightedHtml = html
            .replace(/(\/.+\.php)(\(\d+\))/g, '<span class="text-green-600 dark:text-green-400">$1</span><span class="text-yellow-600 dark:text-yellow-400">$2</span>')
            .replace(/(#\d+)/g, '<span class="text-indigo-600 dark:text-indigo-400">$1</span>')
            .replace(/(\w+\\{2}\w+)/g, '<span class="text-blue-600 dark:text-blue-400">$1</span>');
            
        element.innerHTML = highlightedHtml;
    }
});
</script>
@endsection 