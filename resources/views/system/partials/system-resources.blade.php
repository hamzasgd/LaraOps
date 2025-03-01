<div class="bg-white rounded-lg shadow p-4">
    <div class="p-4 border-b">
        <h2 class="text-xl font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            System Resources
            <span class="ml-2 text-sm text-gray-500" id="resource-time">Last updated: Just now</span>
            <button id="refresh-resources" class="ml-auto p-1 text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </h2>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-1 gap-6">
            <!-- Memory Usage -->
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">Memory Usage</span>
                    <span class="text-sm font-medium" id="memory-text">Loading...</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" id="memory-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- CPU Usage -->
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">CPU Usage</span>
                    <span class="text-sm font-medium" id="cpu-text">Loading...</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" id="cpu-bar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
