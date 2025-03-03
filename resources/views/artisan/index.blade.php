@extends('laravelops::layouts.app')

@section('content')
<div class="h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="p-4 border-b bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Artisan Command Center</h1>
            <div class="flex items-center space-x-4">
                <button class="theme-toggle p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Command List -->
        <div class="w-1/4 bg-white dark:bg-gray-800 border-r overflow-y-auto">
            <div class="p-4">
                <div class="relative">
                    <input type="text" id="command-search" class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Search commands...">
                    <div id="search-results" class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg hidden"></div>
                </div>
            </div>
            <div class="command-list">
                @foreach($commands as $namespace => $namespaceCommands)
                    <div class="namespace-group">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 border-b font-medium cursor-pointer flex items-center justify-between namespace-header">
                            <span>{{ $namespace }}</span>
                            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400 transform transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="namespace-commands hidden">
                            @foreach($namespaceCommands as $command)
                                <div class="p-3 border-b hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer command-item" 
                                     data-command="{{ $command['name'] }}"
                                     data-description="{{ $command['description'] }}"
                                     data-arguments="{{ json_encode($command['synopsis']['arguments']) }}"
                                     data-options="{{ json_encode($command['synopsis']['options']) }}">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $command['name'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $command['description'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Command Panel -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Command Details -->
            <div class="flex-1 p-6 overflow-y-auto">
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4" id="selected-command">Select a Command</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6" id="command-description">Choose a command from the left panel to get started</p>
                        <div class="space-y-6">
                            <div id="arguments-container"></div>
                            <div id="options-container"></div>
                        </div>
                        <form id="command-form">
                            <button type="submit" class="mt-6 w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-all flex items-center justify-center" id="run-command" disabled>
                                <span class="mr-2">Run Command</span>
                                <svg id="loading-icon" class="hidden h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Output Panel -->
            <div class="bg-gray-900 p-4 border-t border-gray-800">
                <div class="max-w-3xl mx-auto">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-medium text-gray-200">Output</h3>
                        <button onclick='handleCopyButtonClick(this)' class="p-2 bg-gray-700 text-gray-100 rounded-lg hover:bg-gray-600">
                            <i class='fas fa-clipboard'></i>
                        </button>
                    </div>
                    <div style="height: 400px; overflow-y: auto;">
                        <pre id="command-output" class="p-4 bg-gray-800 text-gray-100 rounded-lg h-full whitespace-pre-wrap"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const initializeUI = () => {
    try {
        // Initialize accordions
        const headers = document.querySelectorAll('.namespace-header');
        if (headers) {
            headers.forEach(header => {
                header.addEventListener('click', function() {
                    const commands = this.nextElementSibling;
                    if (commands) {
                        commands.classList.toggle('hidden');
                        const icon = this.querySelector('svg');
                        if (icon) {
                            icon.classList.toggle('rotate-180');
                        }
                    }
                });
            });
        }
    } catch (error) {
        console.error('UI initialization error:', error);
    }
};

const initializeCommandSelection = () => {
    const commandItems = document.querySelectorAll('.command-item');
    if (!commandItems) return;

    const selectedCommand = document.getElementById('selected-command');
    const commandDescription = document.getElementById('command-description');
    const argumentsContainer = document.getElementById('arguments-container');
    const optionsContainer = document.getElementById('options-container');
    const runCommandBtn = document.getElementById('run-command');

    commandItems.forEach(item => {
        item.addEventListener('click', function() {
            // Clear previous selection
            commandItems.forEach(i => i.classList.remove('bg-blue-50', 'text-white'));
            this.classList.add('bg-blue-50', 'text-white');

            // Update command details
            if (selectedCommand && commandDescription) {
                selectedCommand.textContent = this.dataset.command;
                commandDescription.textContent = this.dataset.description;
            }

            // Build arguments form
            if (argumentsContainer) {
                argumentsContainer.innerHTML = '<h3 class="text-lg font-medium mt-4 mb-2">Arguments</h3>';
                const arguments = JSON.parse(this.dataset.arguments);
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
            }

            // Build options form
            if (optionsContainer) {
                optionsContainer.innerHTML = '<h3 class="text-lg font-medium mt-4 mb-2">Options</h3>';
                const options = JSON.parse(this.dataset.options);
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
            }

            // Enable run button
            if (runCommandBtn) {
                runCommandBtn.disabled = false;
            }
        });
    });
};

const initializeCommandExecution = () => {
    const commandForm = document.getElementById('command-form');
    if (!commandForm) return;

    commandForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const runCommandBtn = document.getElementById('run-command');
        const commandOutput = document.getElementById('command-output');
        const loadingIcon = document.getElementById('loading-icon');
        const selectedCommand = document.getElementById('selected-command');

        if (!runCommandBtn || !commandOutput || !loadingIcon || !selectedCommand) return;

        try {
            // Disable button and show loading state
            runCommandBtn.disabled = true;
            loadingIcon.classList.remove('hidden');

            // Validate required fields
            const requiredInputs = commandForm.querySelectorAll('input[required]');
            let isValid = true;
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                throw new Error('Please fill out all required fields');
            }

            // Prepare payload
            const payload = {
                command: selectedCommand.textContent,
                arguments: {},
                options: {}
            };

            // Collect arguments
            commandForm.querySelectorAll('input[name^="arguments"]').forEach(input => {
                payload.arguments[input.name.replace('arguments[', '').replace(']', '')] = input.value;
            });

            // Collect options
            commandForm.querySelectorAll('input[name^="options"]').forEach(input => {
                if (input.type === 'checkbox') {
                    payload.options[input.name.replace('options[', '').replace(']', '')] = input.checked;
                } else {
                    payload.options[input.name.replace('options[', '').replace(']', '')] = input.value;
                }
            });

            // Validate payload
            if (!payload.command) {
                throw new Error('No command selected');
            }

            const response = await fetch('{{ route('laravelops.artisan.execute') }}', {
                method: 'POST',
                body: JSON.stringify(payload),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            commandOutput.textContent = data.output;
            commandOutput.scrollTop = commandOutput.scrollHeight;

            // Update output styling based on status
            commandOutput.className = 'p-4 bg-gray-900 text-gray-100 rounded-lg';
            if (data.status === 'success') {
                commandOutput.classList.add('text-green-300');
            } else {
                commandOutput.classList.add('text-red-300');
            }
        } catch (error) {
            console.error('Command execution error:', error);
            commandOutput.textContent = 'Error: ' + error.message;
            commandOutput.className = 'p-4 bg-gray-900 text-red-300 rounded-lg';
        } finally {
            runCommandBtn.disabled = false;
            loadingIcon.classList.add('hidden');
        }
    });
};

const initializeCopyButton = () => {
    const copyButton = document.getElementById('copy-button');
    if (!copyButton) return;

    copyButton.addEventListener('click', function() {
        const output = document.getElementById('command-output').innerText;
        navigator.clipboard.writeText(output).then(() => {
            document.getElementById('copy-icon').innerHTML = '<path fill="currentColor" d="M10 15l-5-5h3V7h4v3h3l-5 5z" />';
        }).catch(() => {
            console.error('Failed to copy.');
        });
    });
};

const initializeSearch = () => {
    const searchInput = document.getElementById('command-search');
    const searchResults = document.getElementById('search-results');
    const commandItems = document.querySelectorAll('.command-item');

    if (!searchInput || !searchResults || !commandItems) return;

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let resultsFound = false;

        // Clear previous results
        searchResults.innerHTML = '';

        // Search through commands
        commandItems.forEach(item => {
            const commandName = item.dataset.command.toLowerCase();
            const commandDesc = item.dataset.description.toLowerCase();

            if (commandName.includes(searchTerm) || commandDesc.includes(searchTerm)) {
                if (!resultsFound) {
                    searchResults.classList.remove('hidden');
                    resultsFound = true;
                }

                const clone = item.cloneNode(true);
                clone.classList.remove('command-item');
                clone.classList.add('p-2', 'hover:bg-gray-100', 'dark:hover:bg-gray-700', 'cursor-pointer');
                clone.addEventListener('click', () => {
                    item.click();
                    searchResults.classList.add('hidden');
                    searchInput.value = '';
                });
                searchResults.appendChild(clone);
            }
        });

        if (!resultsFound && searchTerm.length > 0) {
            searchResults.innerHTML = '<div class="p-2 text-gray-500">No commands found</div>';
            searchResults.classList.remove('hidden');
        } else if (searchTerm.length === 0) {
            searchResults.classList.add('hidden');
        }
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && !searchInput.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
};

function handleCopyButtonClick(button) {
    const textToCopy = document.getElementById('command-output').innerText;
    const originalIcon = button.innerHTML;
    
    navigator.clipboard.writeText(textToCopy).then(() => {
        button.innerHTML = `<i class='fas fa-check text-green-500'></i>`;
        setTimeout(() => {
            button.innerHTML = originalIcon;
        }, 3000);
    }).catch(() => {
        button.innerHTML = `<i class='fas fa-times text-red-500'></i>`;
        setTimeout(() => {
            button.innerHTML = originalIcon;
        }, 3000);
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initializeUI();
    initializeCommandSelection();
    initializeCommandExecution();
    initializeCopyButton();
    initializeSearch();
});
</script>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
@endsection