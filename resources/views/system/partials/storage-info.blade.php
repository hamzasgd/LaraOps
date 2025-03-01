<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            Storage Information
        </h2>
    </div>
    <div class="p-5">
        @if(isset($storageInfo) && is_array($storageInfo) && count($storageInfo) > 0)
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center">
                <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Storage Link</div>
                    <div class="font-medium text-gray-900 dark:text-white">
                        @if(isset($storageInfo['Storage Link']) && $storageInfo['Storage Link'] === 'Not Created')
                            <span class="text-red-600 dark:text-red-400">Not Created</span>
                        @else
                            <span class="text-green-600 dark:text-green-400">Created</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($storageInfo as $key => $value)
                    @if($key !== 'Storage Link')
                        <div class="px-4 py-3 flex justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $key }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $value }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @else
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">No storage information available</p>
        </div>
        @endif
        
        @if(isset($directoryPermissions) && is_array($directoryPermissions) && count($directoryPermissions) > 0)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Directory Permissions</h3>
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Directory</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permission</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($directoryPermissions as $dir => $permission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $dir }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $permission['permission'] }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($permission['writable'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                Writable
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                Not Writable
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Directory Permissions</h3>
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">No directory permission information available</p>
            </div>
        </div>
        @endif
    </div>
</div> 