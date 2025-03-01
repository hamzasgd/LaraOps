<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            System Resources
            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">(Auto-refreshes every 5 seconds)</span>
        </h2>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Memory Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Memory Usage</h3>
                    <span id="memory-text" class="text-sm font-medium text-gray-900 dark:text-white">
                        @if(isset($systemResources['memory']) && isset($systemResources['memory']['percentage']))
                            {{ $systemResources['memory']['formatted'] }} ({{ $systemResources['memory']['percentage'] }}%)
                        @else
                            Loading...
                        @endif
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-1">
                    <div id="memory-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: {{ isset($systemResources['memory']['percentage']) ? $systemResources['memory']['percentage'] : 0 }}%"></div>
                </div>
            </div>
            
            <!-- CPU Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">CPU Usage</h3>
                    <span id="cpu-text" class="text-sm font-medium text-gray-900 dark:text-white">
                        @if(isset($systemResources['cpu']) && isset($systemResources['cpu']['percentage']))
                            {{ $systemResources['cpu']['percentage'] }}%
                        @else
                            Loading...
                        @endif
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-1">
                    <div id="cpu-bar" class="bg-green-600 h-2.5 rounded-full" style="width: {{ isset($systemResources['cpu']['percentage']) ? $systemResources['cpu']['percentage'] : 0 }}%"></div>
                </div>
            </div>
            
            <!-- Disk Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Disk Usage</h3>
                    <span id="disk-text" class="text-sm font-medium text-gray-900 dark:text-white">
                        @if(isset($systemResources['disk']) && isset($systemResources['disk']['percentage']))
                            {{ $systemResources['disk']['formatted'] }} ({{ $systemResources['disk']['percentage'] }}%)
                        @else
                            Loading...
                        @endif
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-1">
                    <div id="disk-bar" class="bg-purple-600 h-2.5 rounded-full" style="width: {{ isset($systemResources['disk']['percentage']) ? $systemResources['disk']['percentage'] : 0 }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">System Load</h3>
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[120px]">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">1 minute</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="load-1">
                            @if(isset($systemResources['load']) && isset($systemResources['load'][0]))
                                {{ $systemResources['load'][0] }}
                            @else
                                <span class="text-gray-400">Loading...</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-[120px]">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">5 minutes</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="load-5">
                            @if(isset($systemResources['load']) && isset($systemResources['load'][1]))
                                {{ $systemResources['load'][1] }}
                            @else
                                <span class="text-gray-400">Loading...</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-[120px]">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">15 minutes</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="load-15">
                            @if(isset($systemResources['load']) && isset($systemResources['load'][2]))
                                {{ $systemResources['load'][2] }}
                            @else
                                <span class="text-gray-400">Loading...</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Function to fetch system resources
function fetchSystemResources() {
    fetch('/laravelops/system/resources')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                updateMemoryUsage(data.memory);
                updateCpuUsage(data.cpu);
                updateDiskUsage(data.disk);
                updateSystemLoad(data.load);
            }
        })
        .catch(error => {
            console.error('Error fetching system resources:', error);
        });
}

// Update system load display
function updateSystemLoad(load) {
    if (load && Array.isArray(load) && load.length >= 3) {
        document.getElementById('load-1').textContent = load[0];
        document.getElementById('load-5').textContent = load[1];
        document.getElementById('load-15').textContent = load[2];
    }
}

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
    }
}

// Fetch resources initially and then every 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Initial fetch
    fetchSystemResources();
    
    // Set up interval for auto-refresh
    setInterval(fetchSystemResources, 5000);
});
</script>
@endpush
