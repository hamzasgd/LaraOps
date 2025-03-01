<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b">
        <h2 class="text-xl font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            System Information
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($systemInfo as $key => $value)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ $key }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 break-words">
                            {{ $value }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> 