@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Application Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Overview of your Laravel application and server environment</p>
    </div>
    
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') }}
    </div>
    @endif
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System Resources - Moved to the top -->
        <div class="col-span-1 lg:col-span-2">
            @include('laravelops::system.partials.system-resources')
        </div>
        
        <!-- Quick Actions - Moved to the top -->
        <div class="col-span-1 lg:col-span-2">
            @include('laravelops::system.partials.quick-actions')
        </div>
        
        <!-- Health Status -->
        <div class="col-span-1 lg:col-span-2">
            @include('laravelops::system.partials.health-status')
        </div>
        
        <!-- Laravel Information -->
        <div>
            @include('laravelops::system.partials.laravel-info')
        </div>
        
        <!-- Database Information -->
        <div>
            @include('laravelops::system.partials.database-info')
        </div>
        
        <!-- System Information -->
        <div>
            @include('laravelops::system.partials.system-info')
        </div>
        
        <!-- Web Server Information -->
        <div>
            @include('laravelops::system.partials.webserver-info')
        </div>
        
        <!-- Storage Information -->
        <div>
            @include('laravelops::system.partials.storage-info')
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure the system resources get loaded on page load
    if (typeof fetchSystemResources === 'function') {
        fetchSystemResources();
        
        // Auto-refresh every 30 seconds
        setInterval(fetchSystemResources, 30000);
    }
    
    // Other existing script code
    // ...
});

// Update memory usage display
function updateMemoryUsage(memory) {
    const memoryBar = document.getElementById('memory-bar');
    const memoryText = document.getElementById('memory-text');
    
    if (memory && memory.percentage !== null) {
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
    } else if (memory) {
        memoryText.textContent = memory.formatted;
    }
}

// Update CPU usage display
function updateCpuUsage(cpu) {
    const cpuBar = document.getElementById('cpu-bar');
    const cpuText = document.getElementById('cpu-text');
    
    if (cpu && cpu.percentage !== null) {
        cpuBar.style.width = cpu.percentage + '%';
        cpuText.textContent = cpu.percentage + '%';
        
        // Change color based on usage
        if (cpu.percentage > 90) {
            cpuBar.classList.remove('bg-green-600', 'bg-yellow-500');
            cpuBar.classList.add('bg-red-600');
        } else if (cpu.percentage > 70) {
            cpuBar.classList.remove('bg-green-600', 'bg-red-600');
            cpuBar.classList.add('bg-yellow-500');
        } else {
            cpuBar.classList.remove('bg-yellow-500', 'bg-red-600');
            cpuBar.classList.add('bg-green-600');
        }
    } else if (cpu) {
        cpuText.textContent = 'N/A';
    }
}

// Update disk usage display
function updateDiskUsage(disk) {
    const diskBar = document.getElementById('disk-bar');
    const diskText = document.getElementById('disk-text');
    
    if (disk && disk.percentage !== null) {
        diskBar.style.width = disk.percentage + '%';
        diskText.textContent = disk.formatted + ' (' + disk.percentage + '%)';
        
        // Change color based on usage
        if (disk.percentage > 90) {
            diskBar.classList.remove('bg-purple-600', 'bg-yellow-500');
            diskBar.classList.add('bg-red-600');
        } else if (disk.percentage > 70) {
            diskBar.classList.remove('bg-purple-600', 'bg-red-600');
            diskBar.classList.add('bg-yellow-500');
        } else {
            diskBar.classList.remove('bg-yellow-500', 'bg-red-600');
            diskBar.classList.add('bg-purple-600');
        }
    } else if (disk) {
        diskText.textContent = disk.formatted;
    }
}
</script>
@endpush
@endsection
