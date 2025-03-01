<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b">
        <h2 class="text-xl font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
            Database Information
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($databaseInfo as $key => $value)
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