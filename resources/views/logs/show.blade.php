@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Log File: {{ $filename }}</h1>
        <a href="{{ route('laravelops.logs.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to Files
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @forelse($logs as $log)
            <div class="p-4 border-b {{ $log['level'] === 'ERROR' ? 'bg-red-50' : 'bg-white' }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">{{ $log['datetime'] }}</span>
                    <span class="px-2 py-1 text-xs rounded
                        @if($log['level'] === 'ERROR') bg-red-100 text-red-800
                        @elseif($log['level'] === 'WARNING') bg-yellow-100 text-yellow-800
                        @elseif($log['level'] === 'INFO') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $log['level'] }}
                    </span>
                </div>
                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ $log['message'] }}</pre>
            </div>
        @empty
            <div class="p-4 text-center text-gray-500">No log entries found</div>
        @endforelse
    </div>
</div>
@endsection 