<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
            Database Information
        </h2>
    </div>
    <div class="p-5">
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center">
                <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Connection</div>
                    <div class="font-medium text-gray-900 dark:text-white">{{ $databaseInfo['Connection'] ?? 'Unknown' }}</div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($databaseInfo as $key => $value)
                    @if($key !== 'Connection')
                        <div class="px-4 py-3 flex justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $key }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $value }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
            <span class="text-sm text-gray-500 dark:text-gray-400">Database Size</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $databaseSize ?? 'Unknown' }}</span>
        </div>
        
        @if(isset($databaseTables) && count($databaseTables) > 0)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tables</h3>
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Table Name</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rows</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Size</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($databaseTables as $table)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $table['name'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $table['rows'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $table['size'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">No table information available</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleAccordion(id) {
    const content = document.getElementById(id);
    const icon = document.getElementById(id + 'Icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

// Open the first accordion by default
document.addEventListener('DOMContentLoaded', function() {
    toggleAccordion('dbConnection');
});
</script>
@endpush 