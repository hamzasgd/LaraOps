<div class="bg-white rounded-lg shadow p-4">
    <div class="p-4 border-b">
        <h2 class="text-xl font-semibold">Quick Actions</h2>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <form action="{{ route('laravelops.system.clear-cache') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center justify-center text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear Cache
                </button>
            </form>

            <form action="{{ route('laravelops.system.clear-views') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center justify-center text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Clear Views
                </button>
            </form>

            <form action="{{ route('laravelops.system.clear-routes') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center justify-center text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Clear Routes
                </button>
            </form>

            @if(isset($storageInfo['Storage Link']) && $storageInfo['Storage Link'] === 'Not Created')
            <form action="{{ route('laravelops.system.create-link') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center justify-center text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Create Storage Link
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
