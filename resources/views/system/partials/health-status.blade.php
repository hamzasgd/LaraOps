<div class="bg-white rounded-lg shadow p-4">
    <div class="p-4 border-b">
        <h2 class="text-xl font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2 {{ $criticalIssues > 0 ? 'text-red-500' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            System Health
            @if($criticalIssues > 0)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $criticalIssues }} {{ Str::plural('issue', $criticalIssues) }} to fix
                </span>
            @else
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    All systems operational
                </span>
            @endif
        </h2>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($healthChecks as $check)
                <div class="border rounded p-3 flex items-center
                    {{ $check['status'] === 'critical' ? 'border-red-300 bg-red-50' :
                       ($check['status'] === 'warning' ? 'border-yellow-300 bg-yellow-50' : 'border-green-300 bg-green-50') }}">
                    <div class="mr-3">
                        @if($check['status'] === 'critical')
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($check['status'] === 'warning')
                            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="font-medium
                            {{ $check['status'] === 'critical' ? 'text-red-700' :
                               ($check['status'] === 'warning' ? 'text-yellow-700' : 'text-green-700') }}">
                            {{ $check['name'] }}
                        </div>
                        <div class="text-sm
                            {{ $check['status'] === 'critical' ? 'text-red-600' :
                               ($check['status'] === 'warning' ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $check['message'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
