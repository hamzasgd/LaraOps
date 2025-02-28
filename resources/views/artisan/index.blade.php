@extends('laravelops::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Artisan Commands</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <input type="text" id="command-search" class="w-full px-3 py-2 border rounded" placeholder="Search commands...">
                </div>
                <div class="command-list" style="height: 600px; overflow-y: auto;">
                    @foreach($commands as $namespace => $namespaceCommands)
                        <div class="namespace-group">
                            <div class="p-3 bg-gray-50 border-b font-medium cursor-pointer namespace-header">
                                {{ $namespace }}
                                <svg class="float-right h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="namespace-commands hidden">
                                @foreach($namespaceCommands as $command)
                                    <div class="p-3 border-b hover:bg-gray-50 cursor-pointer command-item" 
                                         data-command="{{ $command['name'] }}"
                                         data-description="{{ $command['description'] }}"
                                         data-arguments="{{ json_encode($command['synopsis']['arguments']) }}"
                                         data-options="{{ json_encode($command['synopsis']['options']) }}">
                                        <div class="font-medium">{{ $command['name'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $command['description'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold" id="selected-command">Select a command</h2>
                    <p class="text-gray-600" id="command-description"></p>
                </div>
                <div class="p-4">
                    <form id="command-form">
                        <div id="arguments-container"></div>
                        <div id="options-container"></div>
                        
                        <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50" id="run-command" disabled>Run Command</button>
                    </form>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold">Output</h2>
                </div>
                <div class="p-4">
                    <pre id="command-output" class="p-3 bg-gray-800 text-gray-100 rounded" style="height: 300px; overflow-y: auto;"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle namespace groups
    document.querySelectorAll('.namespace-header').forEach(header => {
        header.addEventListener('click', function() {
            const commands = this.nextElementSibling;
            commands.classList.toggle('hidden');
        });
    });

    const commandItems = document.querySelectorAll('.command-item');
    const selectedCommand = document.getElementById('selected-command');
    const commandDescription = document.getElementById('command-description');
    const argumentsContainer = document.getElementById('arguments-container');
    const optionsContainer = document.getElementById('options-container');
    const runCommandBtn = document.getElementById('run-command');
    const commandOutput = document.getElementById('command-output');
    const commandForm = document.getElementById('command-form');
    const commandSearch = document.getElementById('command-search');
    
    // Search functionality
    commandSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        document.querySelectorAll('.namespace-group').forEach(group => {
            let hasVisibleCommands = false;
            
            group.querySelectorAll('.command-item').forEach(item => {
                const commandName = item.dataset.command.toLowerCase();
                const commandDesc = item.dataset.description.toLowerCase();
                const isVisible = commandName.includes(searchTerm) || commandDesc.includes(searchTerm);
                
                item.style.display = isVisible ? 'block' : 'none';
                if (isVisible) hasVisibleCommands = true;
            });
            
            // Show/hide namespace group based on whether it has visible commands
            group.style.display = hasVisibleCommands ? 'block' : 'none';
            
            // Expand the namespace if there's a search term
            if (searchTerm && hasVisibleCommands) {
                group.querySelector('.namespace-commands').classList.remove('hidden');
            }
        });
    });
    
    // Command selection
    commandItems.forEach(item => {
        item.addEventListener('click', function() {
            // Clear active state
            commandItems.forEach(i => i.classList.remove('bg-blue-50'));
            this.classList.add('bg-blue-50');
            
            const command = this.dataset.command;
            const description = this.dataset.description;
            const arguments = JSON.parse(this.dataset.arguments);
            const options = JSON.parse(this.dataset.options);
            
            selectedCommand.textContent = command;
            commandDescription.textContent = description;
            
            // Build arguments form
            argumentsContainer.innerHTML = '<h3 class="text-lg font-medium mt-4 mb-2">Arguments</h3>';
            if (arguments.length === 0) {
                argumentsContainer.innerHTML += '<p class="text-gray-500">No arguments</p>';
            } else {
                arguments.forEach(arg => {
                    argumentsContainer.innerHTML += `
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">${arg.name}${arg.required ? ' <span class="text-red-500">*</span>' : ''}</label>
                            <input type="text" class="w-full px-3 py-2 border rounded" name="arguments[${arg.name}]" ${arg.required ? 'required' : ''}>
                            <p class="mt-1 text-sm text-gray-500">${arg.description}</p>
                        </div>
                    `;
                });
            }
            
            // Build options form
            optionsContainer.innerHTML = '<h3 class="text-lg font-medium mt-4 mb-2">Options</h3>';
            if (options.length === 0) {
                optionsContainer.innerHTML += '<p class="text-gray-500">No options</p>';
            } else {
                options.forEach(option => {
                    if (option.accepts_value) {
                        optionsContainer.innerHTML += `
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">--${option.name}${option.shortcut ? ' (-' + option.shortcut + ')' : ''}</label>
                                <input type="text" class="w-full px-3 py-2 border rounded" name="options[${option.name}]" value="${option.default || ''}">
                                <p class="mt-1 text-sm text-gray-500">${option.description}</p>
                            </div>
                        `;
                    } else {
                        optionsContainer.innerHTML += `
                            <div class="mb-4 flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" class="h-4 w-4 border-gray-300 rounded" id="option-${option.name}" name="options[${option.name}]">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="option-${option.name}" class="font-medium">--${option.name}${option.shortcut ? ' (-' + option.shortcut + ')' : ''}</label>
                                    <p class="text-gray-500">${option.description}</p>
                                </div>
                            </div>
                        `;
                    }
                });
            }
            
            runCommandBtn.disabled = false;
        });
    });
    
    // Command execution
    commandForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('command', selectedCommand.textContent);
        
        runCommandBtn.disabled = true;
        runCommandBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Running...';
        
        fetch('{{ route('laravelops.artisan.execute') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            commandOutput.textContent = data.output;
            commandOutput.scrollTop = commandOutput.scrollHeight;
            
            // Add status class
            commandOutput.className = 'p-3 rounded';
            if (data.status === 'success') {
                commandOutput.classList.add('bg-gray-800', 'text-green-300');
            } else {
                commandOutput.classList.add('bg-gray-800', 'text-red-300');
            }
        })
        .catch(error => {
            commandOutput.textContent = 'Error: ' + error.message;
            commandOutput.className = 'p-3 bg-gray-800 text-red-300 rounded';
        })
        .finally(() => {
            runCommandBtn.disabled = false;
            runCommandBtn.innerHTML = 'Run Command';
        });
    });
});
</script>
@endsection 