@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Laravel Tinker</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Code Editor</h2>
                    <div class="flex space-x-2">
                        <button id="clear-editor-btn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            Clear
                        </button>
                        <button id="run-code-btn" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Run Code
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <div id="editor" class="border rounded" style="height: 300px;">// Write your PHP code here
// Example:
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo $user->name . "\n";}</div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Output</h2>
                    <button id="clear-output-btn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Clear
                    </button>
                </div>
                <div class="p-4">
                    <pre id="output" class="p-3 bg-gray-800 text-gray-100 rounded" style="height: 200px; overflow-y: auto;"></pre>
                </div>
            </div>
        </div>
        
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold">History</h2>
                </div>
                <div class="p-0">
                    <ul id="history-list" class="divide-y">
                        <li class="p-4 text-gray-500 text-center">Loading history...</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold">Snippets</h2>
                </div>
                <div class="p-0">
                    <ul class="divide-y">
                        <li class="p-3 hover:bg-gray-50 cursor-pointer snippet-item">
                            <div class="font-medium">Get all users</div>
                            <div class="text-sm text-gray-600">List all users in the database</div>
                            <pre class="hidden">$users = \App\Models\User::all();
foreach ($users as $user) {
    echo $user->name . " (" . $user->email . ")\n";
}</pre>
                        </li>
                        <li class="p-3 hover:bg-gray-50 cursor-pointer snippet-item">
                            <div class="font-medium">Application version</div>
                            <div class="text-sm text-gray-600">Get Laravel and PHP versions</div>
                            <pre class="hidden">echo "Laravel Version: " . app()->version() . "\n";
echo "PHP Version: " . phpversion() . "\n";</pre>
                        </li>
                        <li class="p-3 hover:bg-gray-50 cursor-pointer snippet-item">
                            <div class="font-medium">Database query</div>
                            <div class="text-sm text-gray-600">Run a raw database query</div>
                            <pre class="hidden">$results = DB::select('SELECT * FROM users LIMIT 5');
print_r($results);</pre>
                        </li>
                        <li class="p-3 hover:bg-gray-50 cursor-pointer snippet-item">
                            <div class="font-medium">Cache inspection</div>
                            <div class="text-sm text-gray-600">Check if a cache key exists</div>
                            <pre class="hidden">$key = 'your-cache-key';
if (Cache::has($key)) {
    echo "Cache key exists: " . Cache::get($key);
} else {
    echo "Cache key does not exist";
}</pre>
                        </li>
                        <li class="p-3 hover:bg-gray-50 cursor-pointer snippet-item">
                            <div class="font-medium">Route list</div>
                            <div class="text-sm text-gray-600">Get all registered routes</div>
                            <pre class="hidden">$routes = Route::getRoutes();
foreach ($routes as $route) {
    echo $route->uri() . " [" . implode("|", $route->methods()) . "]\n";
}</pre>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Ace Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Ace Editor
    const editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/php");
    editor.setOptions({
        fontSize: "12pt",
        showPrintMargin: false,
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true
    });
    
    const outputEl = document.getElementById('output');
    const runCodeBtn = document.getElementById('run-code-btn');
    const clearEditorBtn = document.getElementById('clear-editor-btn');
    const clearOutputBtn = document.getElementById('clear-output-btn');
    const historyList = document.getElementById('history-list');
    const snippetItems = document.querySelectorAll('.snippet-item');
    
    // Load history
    loadHistory();
    
    // Run code
    runCodeBtn.addEventListener('click', function() {
        const code = editor.getValue();
        if (!code.trim()) return;
        
        runCodeBtn.disabled = true;
        runCodeBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Running...';
        
        fetch('{{ route('laravelops.tinker.execute') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            outputEl.textContent = data.output || 'No output';
            outputEl.scrollTop = outputEl.scrollHeight;
            
            // Add to history
            saveToHistory(code);
        })
        .catch(error => {
            outputEl.textContent = 'Error: ' + error.message;
        })
        .finally(() => {
            runCodeBtn.disabled = false;
            runCodeBtn.textContent = 'Run Code';
        });
    });
    
    // Clear editor
    clearEditorBtn.addEventListener('click', function() {
        editor.setValue('');
    });
    
    // Clear output
    clearOutputBtn.addEventListener('click', function() {
        outputEl.textContent = '';
    });
    
    // Snippet items
    snippetItems.forEach(item => {
        item.addEventListener('click', function() {
            const code = this.querySelector('pre').textContent;
            editor.setValue(code);
            editor.clearSelection();
        });
    });
    
    // Load history
    function loadHistory() {
        fetch('{{ route('laravelops.tinker.history') }}')
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    historyList.innerHTML = '<li class="p-4 text-gray-500 text-center">No history yet</li>';
                    return;
                }
                
                historyList.innerHTML = '';
                data.forEach(code => {
                    const li = document.createElement('li');
                    li.className = 'p-3 hover:bg-gray-50 cursor-pointer history-item';
                    
                    // Get first line as title
                    const firstLine = code.split('\n')[0].substring(0, 40);
                    
                    li.innerHTML = `
                        <div class="font-medium">${firstLine}${firstLine.length >= 40 ? '...' : ''}</div>
                        <div class="text-sm text-gray-600">${code.split('\n').length} line(s)</div>
                    `;
                    
                    li.addEventListener('click', function() {
                        editor.setValue(code);
                        editor.clearSelection();
                    });
                    
                    historyList.appendChild(li);
                });
            })
            .catch(error => {
                historyList.innerHTML = '<li class="p-4 text-red-500 text-center">Failed to load history</li>';
            });
    }
    
    // Save to history
    function saveToHistory(code) {
        fetch('{{ route('laravelops.tinker.save-history') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(() => {
            loadHistory();
        });
    }
});
</script>
@endsection 