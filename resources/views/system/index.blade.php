@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Application Dashboard</h1>
        <p class="text-gray-600">Overview of your Laravel application and server environment</p>
    </div>
    
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        {{ session('error') }}
    </div>
    @endif
    
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Column - Critical Info -->
        <div class="lg:w-2/3 space-y-6">
            <!-- Health Status -->
            @include('laravelops::system.partials.health-status')
            
            <!-- System Resources -->
            @include('laravelops::system.partials.system-resources')
        </div>
        
        <!-- Right Column - System Info -->
        <div class="lg:w-1/3 space-y-6">
            <!-- System Summary Card -->
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold mb-3">System Summary</h2>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">PHP Version</div>
                            <div class="font-medium">{{ $systemInfo['PHP Version'] }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Laravel Version</div>
                            <div class="font-medium">{{ $laravelInfo['Laravel Version'] }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Environment</div>
                            <div class="font-medium">{{ $laravelInfo['Environment'] }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Database</div>
                            <div class="font-medium">{{ $databaseInfo['Driver'] ?? 'Unknown' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800" onclick="toggleDetails('system-details'); return false;">
                        View all system details
                    </a>
                </div>
            
                <!-- Documentation Links -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-lg font-semibold mb-3">Documentation</h2>
                    <ul class="space-y-2">
                        <li>
                            <a href="https://laravel.com/docs" target="_blank" class="flex items-center text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Laravel Documentation
                            </a>
                        </li>
                        <li>
                            <a href="https://laracasts.com" target="_blank" class="flex items-center text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Laracasts
                            </a>
                        </li>
                        <li>
                            <a href="https://laravel-news.com" target="_blank" class="flex items-center text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                                Laravel News
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Quick Actions -->
                @include('laravelops::system.partials.quick-actions')
            </div>
        </div>
    </div>
    
    <!-- Detailed System Information (Hidden by Default) -->
    <div id="system-details" class="mt-6 hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- System Information -->
            @include('laravelops::system.partials.system-info')
            
            <!-- Laravel Information -->
            @include('laravelops::system.partials.laravel-info')
            
            <!-- Database Information -->
            @include('laravelops::system.partials.database-info')
            
            <!-- Storage Information -->
            @include('laravelops::system.partials.storage-info')
            
            <!-- Web Server Information -->
            @include('laravelops::system.partials.webserver-info')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initial load
    fetchSystemResources();
    
    // Set up refresh button
    document.getElementById('refresh-resources').addEventListener('click', function() {
        fetchSystemResources();
    });
    
    // Auto refresh every 30 seconds
    setInterval(fetchSystemResources, 30000);
});

function toggleDetails(id) {
    const element = document.getElementById(id);
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
    } else {
        element.classList.add('hidden');
    }
}

function fetchSystemResources() {
    fetch('{{ route('laravelops.system.resources') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMemoryUsage(data.memory);
                updateCpuUsage(data.cpu);
                document.getElementById('resource-time').textContent = 'Last updated: ' + data.time;
            }
        })
        .catch(error => {
            console.error('Error fetching system resources:', error);
        });
}

function updateMemoryUsage(memory) {
    const memoryBar = document.getElementById('memory-bar');
    const memoryText = document.getElementById('memory-text');
    
    if (memory.percentage !== null) {
        memoryBar.style.width = memory.percentage + '%';
        memoryText.textContent = memory.formatted + ' (' + memory.percentage + '%)';
        
        // Change color based on usage
        if (memory.percentage > 90) {
            memoryBar.classList.remove('bg-blue-600', 'bg-yellow-500');
            memoryBar.classList.add('bg-red-600');
        } else if (memory.percentage > 70) {
            memoryBar.classList.remove('bg-blue-600', 'bg-red-600');
            memoryBar.classList.add('bg-yellow-500');
        } else {
            memoryBar.classList.remove('bg-yellow-500', 'bg-red-600');
            memoryBar.classList.add('bg-blue-600');
        }
    } else {
        memoryText.textContent = memory.formatted;
    }
}

function updateCpuUsage(cpu) {
    const cpuBar = document.getElementById('cpu-bar');
    const cpuText = document.getElementById('cpu-text');
    
    if (cpu.percentage !== null) {
        cpuBar.style.width = cpu.percentage + '%';
        cpuText.textContent = cpu.formatted;
        
        // Change color based on usage
        if (cpu.percentage > 90) {
            cpuBar.classList.remove('bg-green-600', 'bg-yellow-500');
            cpuBar.classList.add('bg-red-600');
        } else if (memory.percentage > 70) {
            cpuBar.classList.remove('bg-green-600', 'bg-red-600');
            cpuBar.classList.add('bg-yellow-500');
        } else {
            cpuBar.classList.remove('bg-yellow-500', 'bg-red-600');
            cpuBar.classList.add('bg-green-600');
        }
    } else {
        cpuText.textContent = cpu.formatted;
    }
}
</script>
@endsection
