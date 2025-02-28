@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Log Files</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Modified</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($files as $file)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $file['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $file['size'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ date('Y-m-d H:i:s', $file['modified']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('laravelops.logs.show', $file['name']) }}" 
                               class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No log files found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 