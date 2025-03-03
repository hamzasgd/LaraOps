@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Environment Manager
            </h1>
            <p class="text-gray-600 mt-1">Manage your application's environment variables</p>
        </div>
        <div class="flex items-center space-x-2">
            @php
                $criticalCount = count($missingCriticalVariables) + count($invalidCriticalVariables);
            @endphp
            <div class="relative">
                <button id="criticalBellBtn" class="p-2 text-gray-500 hover:text-blue-600 transition-colors rounded-md hover:bg-gray-100 relative" aria-label="View Critical Issues">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if($criticalCount > 0)
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ $criticalCount }}</span>
                    @endif
                </button>
            </div>
            <form action="{{ route('laravelops.env.clear-cache') }}" method="POST" class="relative">
                @csrf
                <button type="submit" class="p-2 text-gray-500 hover:text-blue-600 transition-colors rounded-md hover:bg-gray-100 relative group" aria-label="Clear Cache">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <div class="absolute right-0 bottom-full mb-2 w-32 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                        Clear Config Cache
                    </div>
                </button>
            </form>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex gap-2">
            <div class="flex-grow">
                <input type="text" id="searchInput" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Search environment variables...">
            </div>
            <button id="showCriticalBtn" class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Critical Only
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    @if(count($missingCriticalVariables) > 0 || count($invalidCriticalVariables) > 0)
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg overflow-hidden" id="criticalWarningSection" style="display: none;">
        <div class="p-4 bg-yellow-100 border-b border-yellow-200 flex justify-between">
            <h3 class="text-lg font-semibold text-yellow-800 flex items-center cursor-pointer" id="critical-warning-header">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Critical Environment Variables Warning
                <button class="collapse-toggle ml-2 p-1 text-yellow-600 hover:text-yellow-800 focus:outline-none" aria-expanded="true" aria-controls="critical-warning-content">
                    <svg class="w-5 h-5 transform rotate-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </h3>
            <button id="closeCriticalWarning" class="text-gray-400 hover:text-blue-600 transition-colors p-1.5 rounded-md hover:bg-gray-100" aria-label="Close Critical Warning">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="critical-warning-content">
            <p class="text-yellow-700 p-4 mb-0 border-b border-yellow-200">The following critical environment variables require attention:</p>
        
            @php
                $dbConnection = isset($allEnvVariables['DB_CONNECTION']) ? strtolower($allEnvVariables['DB_CONNECTION']) : null;
                $isSqlite = $dbConnection === 'sqlite';
                $cacheDriver = isset($allEnvVariables['CACHE_DRIVER']) ? $allEnvVariables['CACHE_DRIVER'] : null;
                $cacheStore = isset($allEnvVariables['CACHE_STORE']) ? $allEnvVariables['CACHE_STORE'] : null;
            @endphp
            
            @if($dbConnection)
                <div class="p-4 bg-blue-50 text-blue-700 rounded-md text-sm mx-4 my-2">
                    <strong>Database Connection:</strong> {{ ucfirst($dbConnection) }}
                    @if($isSqlite)
                        <span class="block mt-1">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Note: With SQLite, variables like DB_HOST, DB_PORT, DB_USERNAME, and DB_PASSWORD are not required.
                            Only DB_DATABASE is needed to specify the database file path or ":memory:" for an in-memory database.
                        </span>
                    @elseif($dbConnection === 'mysql')
                        <span class="block mt-1">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            MySQL connection requires DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and typically DB_PASSWORD.
                        </span>
                    @elseif($dbConnection === 'pgsql')
                        <span class="block mt-1">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            PostgreSQL connection requires DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and typically DB_PASSWORD.
                        </span>
                    @elseif($dbConnection === 'sqlsrv')
                        <span class="block mt-1">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            SQL Server connection requires DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and typically DB_PASSWORD.
                        </span>
                    @endif
                </div>
            @endif
            
            @if($cacheDriver || $cacheStore)
                <div class="p-4 bg-blue-50 text-blue-700 rounded-md text-sm mx-4 my-2">
                    <strong>Cache Configuration:</strong>
                    <span class="block mt-1">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        CACHE_DRIVER: {{ $cacheDriver ?? 'Not set' }} 
                        @if($cacheStore)
                            | CACHE_STORE: {{ $cacheStore }}
                        @endif
                    </span>
                    <span class="block mt-1 text-xs">
                        Note: CACHE_DRIVER defines the default cache driver. CACHE_STORE is optional and defaults to CACHE_DRIVER if not specified.
                        They should typically have the same value unless you're using multiple cache stores.
                    </span>
                </div>
            @endif
            
            <div class="divide-y divide-yellow-200">
                @if(count($missingCriticalVariables) > 0)
                <div class="p-4">
                    <h4 class="font-medium text-yellow-800 mb-2 flex justify-between items-center cursor-pointer" id="missing-variables-header">
                        <span id="missing-variables">Missing Variables:</span>
                        <button class="collapse-toggle p-1 text-yellow-600 hover:text-yellow-800 focus:outline-none" aria-expanded="true" aria-controls="missing-variables-content">
                            <svg class="w-5 h-5 transform rotate-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h4>
                    <ul class="space-y-2" id="missing-variables-content">
                        @foreach($missingCriticalVariables as $key => $description)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <span class="font-mono font-medium text-yellow-800">{{ $key }}</span>
                                <p class="text-sm text-yellow-700">{{ $description }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                @if(count($invalidCriticalVariables) > 0)
                <div class="p-4">
                    <h4 class="font-medium text-yellow-800 mb-2 flex justify-between items-center cursor-pointer" id="invalid-variables-header">
                        <span id="invalid-variables">Invalid Variables:</span>
                        <button class="collapse-toggle p-1 text-yellow-600 hover:text-yellow-800 focus:outline-none" aria-expanded="true" aria-controls="invalid-variables-content">
                            <svg class="w-5 h-5 transform rotate-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </h4>
                    <ul class="space-y-2" id="invalid-variables-content">
                        @foreach($invalidCriticalVariables as $key => $message)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <span class="font-mono font-medium text-yellow-800">{{ $key }}</span>
                                <p class="text-sm text-yellow-700">{{ $message }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($groupedVariables as $group => $variables)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    {{ ucfirst($group) }} Variables
                    @php
                        $hasDuplicates = false;
                        foreach ($variables as $key => $value) {
                            if (isset($duplicateKeys[$key])) {
                                $hasDuplicates = true;
                                break;
                            }
                        }
                    @endphp
                    
                    @if($hasDuplicates)
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Duplicates
                    </span>
                    @endif
                </h2>
                <button 
                    class="copy-group-btn text-gray-400 hover:text-blue-600 transition-colors p-1.5 rounded-md hover:bg-gray-100"
                    data-group="{{ $group }}"
                    title="Copy all {{ ucfirst($group) }} variables">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
            <div>
                <div class="px-4 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider grid grid-cols-12">
                    <div class="col-span-5">KEY</div>
                    <div class="col-span-5">VALUE</div>
                    <div class="col-span-2 text-right">ACTION</div>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($variables as $key => $value)
                    <div class="px-4 py-3 grid grid-cols-12 items-center hover:bg-gray-50 {{ isset($duplicateKeys[$key]) ? 'bg-yellow-50' : '' }}">
                        <div class="col-span-5 font-medium {{ isset($duplicateKeys[$key]) ? 'text-yellow-700 flex items-center' : 'text-gray-900' }} truncate">
                            {{ $key }}
                            @if(isset($duplicateKeys[$key]))
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800" title="Duplicate variable found in .env file">
                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                {{ count($duplicateKeys[$key]) }}
                            </span>
                            @endif
                            @if(array_key_exists($key, $criticalVariables))
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" title="Critical environment variable">
                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Critical
                            </span>
                            @endif
                        </div>
                        <div class="col-span-5 {{ isset($duplicateKeys[$key]) ? 'text-yellow-700' : 'text-gray-500' }} break-words">
                            {{ $value }}
                            @if(isset($duplicateKeys[$key]))
                            <div class="mt-1 text-xs text-yellow-600">
                                Other values: 
                                @foreach($duplicateKeys[$key] as $index => $occurrence)
                                    @if($occurrence['value'] != $value)
                                        <span class="font-mono">{{ $occurrence['value'] }}</span>{{ !$loop->last ? ', ' : '' }}
                                    @endif
                                @endforeach
                            </div>
                            @endif
                            @if(array_key_exists($key, $criticalVariables))
                            <div class="mt-1 text-xs text-blue-600">
                                {{ $criticalVariables[$key] }}
                            </div>
                            @endif
                        </div>
                        <div class="col-span-2 text-right">
                            <button 
                                class="copy-btn text-gray-400 hover:text-blue-600 transition-colors p-1.5 rounded-md hover:bg-gray-100"
                                data-key="{{ $key }}"
                                data-value="{{ $value }}"
                                title="Copy {{ $key }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const variableGroups = document.querySelectorAll('.bg-white.rounded-lg');
        const showCriticalBtn = document.getElementById('showCriticalBtn');
        let closeCriticalWarning = document.getElementById('closeCriticalWarning');
        
        // Copy functionality for individual variables
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function() {
                const key = this.getAttribute('data-key');
                const value = this.getAttribute('data-value');
                const textToCopy = `${key}=${value}`;
                
                navigator.clipboard.writeText(textToCopy)
                    .then(() => {
                        // Show success feedback
                        const originalSvg = this.innerHTML;
                        this.innerHTML = `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>`;
                        
                        setTimeout(() => {
                            this.innerHTML = originalSvg;
                        }, 1500);
                    })
                    .catch(err => {
                        console.error('Failed to copy text: ', err);
                    });
            });
        });
        
        // Copy functionality for group variables
        document.querySelectorAll('.copy-group-btn').forEach(button => {
            button.addEventListener('click', function() {
                const group = this.getAttribute('data-group');
                const groupCard = this.closest('.bg-white.rounded-lg');
                const rows = groupCard.querySelectorAll('.grid.grid-cols-12.items-center');
                
                let textToCopy = '';
                rows.forEach(row => {
                    const key = row.querySelector('.col-span-5').textContent.trim();
                    const value = row.querySelector('.col-span-5 ~ .col-span-5').textContent.trim();
                    textToCopy += `${key}=${value}\n`;
                });
                
                navigator.clipboard.writeText(textToCopy)
                    .then(() => {
                        // Show success feedback
                        const originalSvg = this.innerHTML;
                        this.innerHTML = `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>`;
                        
                        setTimeout(() => {
                            this.innerHTML = originalSvg;
                        }, 1500);
                    })
                    .catch(err => {
                        console.error('Failed to copy text: ', err);
                    });
            });
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // First handle the critical variables warning section
            const criticalWarningSection = document.getElementById('criticalWarningSection');
            if (criticalWarningSection) {
                if (searchTerm === '') {
                    // Don't show the warning section when search is cleared
                    // Keep its current state
                    
                    // But make sure all items are visible
                    criticalWarningSection.querySelectorAll('li').forEach(item => {
                        item.style.display = 'flex';
                    });
                } else {
                    // When searching, check if any critical variables match
                    const criticalItems = criticalWarningSection.querySelectorAll('li');
                    let hasMatch = false;
                    
                    criticalItems.forEach(item => {
                        const key = item.querySelector('.font-mono').textContent.toLowerCase();
                        const description = item.querySelector('p').textContent.toLowerCase();
                        const shouldShow = key.includes(searchTerm) || description.includes(searchTerm);
                        
                        item.style.display = shouldShow ? 'flex' : 'none';
                        if (shouldShow) {
                            hasMatch = true;
                        }
                    });
                    
                    // Only show the warning section if there's a match
                    criticalWarningSection.style.display = hasMatch ? 'block' : 'none';
                    
                    // If we're showing the warning section, make sure its parent sections are also visible
                    if (hasMatch) {
                        const missingSection = document.getElementById('missing-variables');
                        const invalidSection = document.getElementById('invalid-variables');
                        
                        if (missingSection) {
                            missingSection.closest('div').style.display = 'block';
                        }
                        
                        if (invalidSection) {
                            invalidSection.closest('div').style.display = 'block';
                        }
                    }
                }
            }
            
            // Then handle the variable groups
            variableGroups.forEach(group => {
                const rows = group.querySelectorAll('.grid.grid-cols-12.items-center');
                let groupHasMatch = false;
                
                rows.forEach(row => {
                    const keyElement = row.querySelector('.col-span-5');
                    if (!keyElement) {
                        return;
                    }
                    const key = keyElement.textContent.toLowerCase();
                    const valueElement = row.querySelector('.col-span-5 ~ .col-span-5');
                    const value = valueElement ? valueElement.textContent.toLowerCase() : '';
                    
                    // Also search in the critical variable description if present
                    const criticalDescription = row.querySelector('.text-xs.text-blue-600');
                    const descriptionText = criticalDescription ? criticalDescription.textContent.toLowerCase() : '';
                    
                    const shouldShow = key.includes(searchTerm) || value.includes(searchTerm) || descriptionText.includes(searchTerm);
                    row.style.display = shouldShow ? 'grid' : 'none';
                    if (shouldShow) {
                        groupHasMatch = true;
                    }
                });

                // Show/hide group based on matches
                group.style.display = groupHasMatch || searchTerm === '' ? 'block' : 'none';
            });
        });
        
        // Critical only button functionality
        let showingOnlyCritical = false;
        showCriticalBtn.addEventListener('click', function() {
            showingOnlyCritical = !showingOnlyCritical;
            
            // Update button appearance
            if (showingOnlyCritical) {
                this.classList.remove('bg-blue-100', 'text-blue-800');
                this.classList.add('bg-blue-600', 'text-white');
            } else {
                this.classList.remove('bg-blue-600', 'text-white');
                this.classList.add('bg-blue-100', 'text-blue-800');
            }
            
            const variableGroups = document.querySelectorAll('.bg-white.rounded-lg');
            const rows = document.querySelectorAll('.grid.grid-cols-12.items-center');
            
            if (showingOnlyCritical) {
                // Show only critical variables
                rows.forEach(row => {
                    const keyElement = row.querySelector('.col-span-5');
                    if (!keyElement) {
                        return;
                    }
                    
                    // Check if the variable is critical
                    const criticalDescription = row.querySelector('.text-xs.text-blue-600');
                    const isCritical = criticalDescription !== null;
                    
                    row.style.display = isCritical ? 'grid' : 'none';
                });
            } else {
                // Show all variables
                rows.forEach(row => {
                    row.style.display = 'grid';
                });
            }
            
            // Update group visibility
            variableGroups.forEach(group => {
                const rows = group.querySelectorAll('.grid.grid-cols-12.items-center');
                let groupHasMatch = false;
                
                rows.forEach(row => {
                    if (row.style.display === 'grid') {
                        groupHasMatch = true;
                    }
                });
                
                group.style.display = groupHasMatch ? 'block' : 'none';
            });
            
            // If search input has text, reapply the search filter
            if (searchInput.value.trim() !== '') {
                searchInput.dispatchEvent(new Event('input'));
            }
        });
        
        // Close critical warning functionality
        if (closeCriticalWarning) {
            closeCriticalWarning.addEventListener('click', function() {
                document.getElementById('criticalWarningSection').style.display = 'none';
            });
        }
        
        // Notification bell functionality
        const criticalBellBtn = document.getElementById('criticalBellBtn');
        if (criticalBellBtn) {
            criticalBellBtn.addEventListener('click', function() {
                const criticalWarningSection = document.getElementById('criticalWarningSection');
                
                if (criticalWarningSection) {
                    // Toggle visibility
                    if (criticalWarningSection.style.display === 'none') {
                        criticalWarningSection.style.display = 'block';
                    } else {
                        criticalWarningSection.style.display = 'none';
                        return; // Don't scroll if we're hiding it
                    }
                    
                    // Scroll to the warning section
                    criticalWarningSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }
        
        // Collapse toggle functionality
        document.querySelectorAll('.collapse-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event bubbling
                
                const contentId = this.getAttribute('aria-controls');
                const content = document.getElementById(contentId);
                
                if (content.style.display === 'none') {
                    content.style.display = 'block';
                    this.setAttribute('aria-expanded', 'true');
                    this.querySelector('svg').classList.remove('rotate-0');
                    this.querySelector('svg').classList.add('rotate-180');
                } else {
                    content.style.display = 'none';
                    this.setAttribute('aria-expanded', 'false');
                    this.querySelector('svg').classList.remove('rotate-180');
                    this.querySelector('svg').classList.add('rotate-0');
                }
            });
        });
        
        // Make section headers also toggle their content
        const criticalHeader = document.getElementById('critical-warning-header');
        const missingHeader = document.getElementById('missing-variables-header');
        const invalidHeader = document.getElementById('invalid-variables-header');
        
        if (criticalHeader) {
            criticalHeader.addEventListener('click', function(e) {
                if (e.target === this || e.target.parentNode === this) {
                    const toggleButton = this.querySelector('.collapse-toggle');
                    if (toggleButton) {
                        toggleButton.click();
                    }
                }
            });
        }
        
        if (missingHeader) {
            missingHeader.addEventListener('click', function(e) {
                if (e.target === this || e.target.parentNode === this) {
                    const toggleButton = this.querySelector('.collapse-toggle');
                    if (toggleButton) {
                        toggleButton.click();
                    }
                }
            });
        }
        
        if (invalidHeader) {
            invalidHeader.addEventListener('click', function(e) {
                if (e.target === this || e.target.parentNode === this) {
                    const toggleButton = this.querySelector('.collapse-toggle');
                    if (toggleButton) {
                        toggleButton.click();
                    }
                }
            });
        }
        
        // Initialize collapsible sections
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial state for collapsible sections
            const criticalContent = document.getElementById('critical-warning-content');
            const missingContent = document.getElementById('missing-variables-content');
            const invalidContent = document.getElementById('invalid-variables-content');
            
            if (criticalContent) criticalContent.style.display = 'block';
            if (missingContent) missingContent.style.display = 'block';
            if (invalidContent) invalidContent.style.display = 'block';
        });
    });
</script>
@endsection
@endsection