@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Environment Variables</h1>
        <div class="flex space-x-2">
            <button id="clear-cache-btn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Clear Config Cache
            </button>
            <input type="text" id="env-search" class="px-3 py-2 border rounded" placeholder="Search variables...">
        </div>
    </div>
    
    <div id="cache-message" class="hidden mb-4 p-4 rounded"></div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b font-medium">
                    Variable Groups
                </div>
                <div class="p-0">
                    <ul class="group-list">
                        <li class="border-b">
                            <a href="#all" class="block p-3 hover:bg-gray-50 group-item active" data-group="all">
                                All Variables <span class="float-right text-gray-500">{{ count($allVariables) }}</span>
                            </a>
                        </li>
                        @foreach($groupedVariables as $groupName => $vars)
                            <li class="border-b">
                                <a href="#{{ $groupName }}" class="block p-3 hover:bg-gray-50 group-item" data-group="{{ $groupName }}">
                                    {{ $groupName }} <span class="float-right text-gray-500">{{ count($vars) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold" id="current-group">All Variables</h2>
                    <p class="text-gray-600">Showing <span id="visible-count">{{ count($allVariables) }}</span> variables</p>
                </div>
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Variable
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Value
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="variables-container">
                            @foreach($allVariables as $key => $value)
                                <tr class="env-row" data-key="{{ $key }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{ $key }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $value }}</code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const groupItems = document.querySelectorAll('.group-item');
    const envRows = document.querySelectorAll('.env-row');
    const currentGroup = document.getElementById('current-group');
    const visibleCount = document.getElementById('visible-count');
    const searchInput = document.getElementById('env-search');
    const clearCacheBtn = document.getElementById('clear-cache-btn');
    const cacheMessage = document.getElementById('cache-message');
    
    // Group filtering
    groupItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state
            groupItems.forEach(i => i.classList.remove('active', 'bg-indigo-50', 'text-indigo-700'));
            this.classList.add('active', 'bg-indigo-50', 'text-indigo-700');
            
            const group = this.dataset.group;
            currentGroup.textContent = group === 'all' ? 'All Variables' : group;
            
            // Filter rows
            filterRows(searchInput.value, group);
        });
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const activeGroup = document.querySelector('.group-item.active').dataset.group;
        filterRows(this.value, activeGroup);
    });
    
    // Clear cache button
    clearCacheBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Clearing...';
        
        fetch('{{ route('laravelops.env.clear-cache') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            cacheMessage.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
            
            if (data.status === 'success') {
                cacheMessage.classList.add('bg-green-100', 'text-green-700');
            } else {
                cacheMessage.classList.add('bg-red-100', 'text-red-700');
            }
            
            cacheMessage.textContent = data.message;
            
            // Hide message after 5 seconds
            setTimeout(() => {
                cacheMessage.classList.add('hidden');
            }, 5000);
        })
        .catch(error => {
            cacheMessage.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
            cacheMessage.classList.add('bg-red-100', 'text-red-700');
            cacheMessage.textContent = 'An error occurred while clearing the cache.';
        })
        .finally(() => {
            clearCacheBtn.disabled = false;
            clearCacheBtn.textContent = 'Clear Config Cache';
        });
    });
    
    // Filter rows based on search term and group
    function filterRows(searchTerm, group) {
        searchTerm = searchTerm.toLowerCase();
        let visibleRowCount = 0;
        
        envRows.forEach(row => {
            const key = row.dataset.key;
            const keyMatches = key.toLowerCase().includes(searchTerm);
            const groupMatches = group === 'all' || getGroupForKey(key) === group;
            
            if (keyMatches && groupMatches) {
                row.style.display = '';
                visibleRowCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        visibleCount.textContent = visibleRowCount;
    }
    
    // Get the group for a key
    function getGroupForKey(key) {
        const prefixes = {
            'APP_': 'APP',
            'DB_': 'DB',
            'MAIL_': 'MAIL',
            'QUEUE_': 'QUEUE',
            'CACHE_': 'CACHE',
            'SESSION_': 'SESSION',
            'REDIS_': 'REDIS',
            'AWS_': 'AWS',
            'LOG_': 'LOG',
            'BROADCAST_': 'BROADCAST'
        };
        
        for (const prefix in prefixes) {
            if (key.startsWith(prefix)) {
                return prefixes[prefix];
            }
        }
        
        return 'Other';
    }
});
</script>
@endsection 