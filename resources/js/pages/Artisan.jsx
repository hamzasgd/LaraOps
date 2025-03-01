import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import { 
    ArrowPathIcon, 
    CommandLineIcon,
    PlayIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ChevronDownIcon,
    ChevronUpIcon
} from '@heroicons/react/24/outline';
import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { tomorrow } from 'react-syntax-highlighter/dist/esm/styles/prism';

const Artisan = () => {
    const [commands, setCommands] = useState([]);
    const [filteredCommands, setFilteredCommands] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCommand, setSelectedCommand] = useState(null);
    const [commandArgs, setCommandArgs] = useState({});
    const [commandOptions, setCommandOptions] = useState({});
    const [executing, setExecuting] = useState(false);
    const [result, setResult] = useState(null);
    const [history, setHistory] = useState([]);
    const [expandedHistory, setExpandedHistory] = useState({});
    const resultRef = useRef(null);

    useEffect(() => {
        fetchCommands();
        fetchHistory();
    }, []);

    useEffect(() => {
        if (searchTerm) {
            const filtered = commands.filter(command => 
                command.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                command.description.toLowerCase().includes(searchTerm.toLowerCase())
            );
            setFilteredCommands(filtered);
        } else {
            setFilteredCommands(commands);
        }
    }, [searchTerm, commands]);

    useEffect(() => {
        if (result && resultRef.current) {
            resultRef.current.scrollIntoView({ behavior: 'smooth' });
        }
    }, [result]);

    const fetchCommands = async () => {
        try {
            setLoading(true);
            const response = await axios.get('/api/laravelops/artisan');
            setCommands(response.data);
            setError(null);
        } catch (err) {
            setError('Failed to load artisan commands');
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const fetchHistory = async () => {
        try {
            const response = await axios.get('/api/laravelops/artisan/history');
            setHistory(response.data);
        } catch (err) {
            console.error('Failed to load command history', err);
        }
    };

    const handleCommandSelect = (command) => {
        setSelectedCommand(command);
        
        // Initialize arguments and options
        const args = {};
        command.arguments.forEach(arg => {
            args[arg.name] = '';
        });
        setCommandArgs(args);
        
        const options = {};
        command.options.forEach(option => {
            if (option.default !== undefined) {
                options[option.name] = option.default;
            } else {
                options[option.name] = option.is_flag ? false : '';
            }
        });
        setCommandOptions(options);
        
        // Clear previous results
        setResult(null);
    };

    const handleArgChange = (name, value) => {
        setCommandArgs(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleOptionChange = (name, value, isFlag = false) => {
        setCommandOptions(prev => ({
            ...prev,
            [name]: isFlag ? !prev[name] : value
        }));
    };

    const executeCommand = async () => {
        if (!selectedCommand) return;
        
        try {
            setExecuting(true);
            setResult(null);
            
            const payload = {
                command: selectedCommand.name,
                arguments: commandArgs,
                options: commandOptions
            };
            
            const response = await axios.post('/api/laravelops/artisan/execute', payload);
            setResult(response.data);
            
            // Refresh history after execution
            fetchHistory();
        } catch (err) {
            setResult({
                success: false,
                output: err.response?.data?.message || 'An error occurred while executing the command'
            });
            console.error(err);
        } finally {
            setExecuting(false);
        }
    };

    const toggleHistoryItem = (id) => {
        setExpandedHistory(prev => ({
            ...prev,
            [id]: !prev[id]
        }));
    };

    const rerunCommand = (historyItem) => {
        const command = commands.find(cmd => cmd.name === historyItem.command);
        if (command) {
            setSelectedCommand(command);
            setCommandArgs(historyItem.arguments || {});
            setCommandOptions(historyItem.options || {});
            setResult(null);
            
            // Scroll to command form
            document.getElementById('command-form')?.scrollIntoView({ behavior: 'smooth' });
        }
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleString();
    };

    const getCommandString = (command, args, options) => {
        let str = `php artisan ${command}`;
        
        // Add arguments
        Object.entries(args || {}).forEach(([key, value]) => {
            if (value) str += ` ${value}`;
        });
        
        // Add options
        Object.entries(options || {}).forEach(([key, value]) => {
            if (key === 'help' || key === 'quiet' || key === 'verbose') return;
            
            if (typeof value === 'boolean') {
                if (value) str += ` --${key}`;
            } else if (value) {
                str += ` --${key}=${value}`;
            }
        });
        
        return str;
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center h-full">
                <div className="text-center">
                    <ArrowPathIcon className="w-12 h-12 mx-auto text-primary-500 animate-spin" />
                    <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">Loading artisan commands...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <p className="text-red-600 dark:text-red-400">{error}</p>
                <button 
                    onClick={fetchCommands}
                    className="mt-2 px-4 py-2 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-md hover:bg-red-200 dark:hover:bg-red-700"
                >
                    Retry
                </button>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Artisan Commands</h1>
                <button 
                    onClick={fetchCommands}
                    className="flex items-center px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <ArrowPathIcon className="w-4 h-4 mr-2" />
                    Refresh
                </button>
            </div>
            
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Command List */}
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4 lg:col-span-1">
                    <div className="mb-4">
                        <label htmlFor="command-search" className="sr-only">Search commands</label>
                        <div className="relative">
                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <CommandLineIcon className="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                id="command-search"
                                type="text"
                                placeholder="Search commands..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                            />
                        </div>
                    </div>
                    
                    <div className="overflow-y-auto max-h-96">
                        <ul className="divide-y divide-gray-200 dark:divide-gray-700">
                            {filteredCommands.map((command) => (
                                <li key={command.name}>
                                    <button
                                        onClick={() => handleCommandSelect(command)}
                                        className={`w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md ${
                                            selectedCommand?.name === command.name ? 'bg-primary-50 dark:bg-primary-900/20' : ''
                                        }`}
                                    >
                                        <div className="font-medium text-gray-900 dark:text-gray-100">{command.name}</div>
                                        <div className="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{command.description}</div>
                                    </button>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
                
                {/* Command Form */}
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4 lg:col-span-2" id="command-form">
                    {selectedCommand ? (
                        <div>
                            <div className="mb-4">
                                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">{selectedCommand.name}</h2>
                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{selectedCommand.description}</p>
                            </div>
                            
                            <form onSubmit={(e) => { e.preventDefault(); executeCommand(); }} className="space-y-4">
                                {/* Arguments */}
                                {selectedCommand.arguments.length > 0 && (
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Arguments</h3>
                                        <div className="space-y-3">
                                            {selectedCommand.arguments.map((arg) => (
                                                <div key={arg.name}>
                                                    <label htmlFor={`arg-${arg.name}`} className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {arg.name}
                                                        {arg.is_required && <span className="text-red-500">*</span>}
                                                    </label>
                                                    <div className="mt-1">
                                                        <input
                                                            id={`arg-${arg.name}`}
                                                            type="text"
                                                            value={commandArgs[arg.name] || ''}
                                                            onChange={(e) => handleArgChange(arg.name, e.target.value)}
                                                            required={arg.is_required}
                                                            placeholder={arg.description}
                                                            className="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                                        />
                                                    </div>
                                                    {arg.description && (
                                                        <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">{arg.description}</p>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                                
                                {/* Options */}
                                {selectedCommand.options.length > 0 && (
                                    <div>
                                        <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options</h3>
                                        <div className="space-y-3">
                                            {selectedCommand.options
                                                .filter(option => !['help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction', 'env'].includes(option.name))
                                                .map((option) => (
                                                    <div key={option.name}>
                                                        {option.is_flag ? (
                                                            <div className="flex items-center">
                                                                <input
                                                                    id={`option-${option.name}`}
                                                                    type="checkbox"
                                                                    checked={!!commandOptions[option.name]}
                                                                    onChange={() => handleOptionChange(option.name, null, true)}
                                                                    className="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
                                                                />
                                                                <label htmlFor={`option-${option.name}`} className="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                                    --{option.name}
                                                                </label>
                                                            </div>
                                                        ) : (
                                                            <div>
                                                                <label htmlFor={`option-${option.name}`} className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    --{option.name}
                                                                </label>
                                                                <div className="mt-1">
                                                                    <input
                                                                        id={`option-${option.name}`}
                                                                        type="text"
                                                                        value={commandOptions[option.name] || ''}
                                                                        onChange={(e) => handleOptionChange(option.name, e.target.value)}
                                                                        placeholder={option.default !== undefined ? `Default: ${option.default}` : ''}
                                                                        className="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                                                    />
                                                                </div>
                                                            </div>
                                                        )}
                                                        {option.description && (
                                                            <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">{option.description}</p>
                                                        )}
                                                    </div>
                                                ))}
                                        </div>
                                    </div>
                                )}
                                
                                <div className="pt-4">
                                    <button
                                        type="submit"
                                        disabled={executing}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {executing ? (
                                            <>
                                                <ArrowPathIcon className="w-5 h-5 mr-2 animate-spin" />
                                                Executing...
                                            </>
                                        ) : (
                                            <>
                                                <PlayIcon className="w-5 h-5 mr-2" />
                                                Execute
                                            </>
                                        )}
                                    </button>
                                </div>
                            </form>
                            
                            {/* Command Preview */}
                            <div className="mt-6">
                                <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Command Preview</h3>
                                <div className="bg-gray-800 rounded-md p-3 overflow-x-auto">
                                    <pre className="text-sm text-gray-200">
                                        {getCommandString(selectedCommand.name, commandArgs, commandOptions)}
                                    </pre>
                                </div>
                            </div>
                            
                            {/* Result */}
                            {result && (
                                <div className="mt-6" ref={resultRef}>
                                    <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Result</h3>
                                    <div className={`rounded-md p-4 ${result.success ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'}`}>
                                        <div className="flex">
                                            <div className="flex-shrink-0">
                                                {result.success ? (
                                                    <CheckCircleIcon className="h-5 w-5 text-green-400" />
                                                ) : (
                                                    <XCircleIcon className="h-5 w-5 text-red-400" />
                                                )}
                                            </div>
                                            <div className="ml-3">
                                                <h3 className={`text-sm font-medium ${result.success ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'}`}>
                                                    {result.success ? 'Command executed successfully' : 'Command execution failed'}
                                                </h3>
                                            </div>
                                        </div>
                                        <div className="mt-4">
                                            <SyntaxHighlighter 
                                                language="bash" 
                                                style={tomorrow}
                                                customStyle={{ margin: 0, borderRadius: '0.375rem' }}
                                            >
                                                {result.output || 'No output'}
                                            </SyntaxHighlighter>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    ) : (
                        <div className="text-center py-12">
                            <CommandLineIcon className="mx-auto h-12 w-12 text-gray-400" />
                            <h3 className="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No Command Selected</h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Select a command from the list to execute it.
                            </p>
                        </div>
                    )}
                </div>
            </div>
            
            {/* Command History */}
            {history.length > 0 && (
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Command History</h2>
                    <div className="space-y-3">
                        {history.map((item, index) => (
                            <div key={index} className="border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden">
                                <div 
                                    className="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex items-center justify-between cursor-pointer"
                                    onClick={() => toggleHistoryItem(index)}
                                >
                                    <div className="flex items-center space-x-3">
                                        <span className={`px-2 py-1 rounded-md text-xs font-medium ${item.success ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'}`}>
                                            {item.success ? 'Success' : 'Failed'}
                                        </span>
                                        <span className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {item.command}
                                        </span>
                                    </div>
                                    <div className="flex items-center space-x-3">
                                        <span className="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                            <ClockIcon className="w-4 h-4 mr-1" />
                                            {formatDate(item.executed_at)}
                                        </span>
                                        {expandedHistory[index] ? (
                                            <ChevronUpIcon className="w-5 h-5 text-gray-400" />
                                        ) : (
                                            <ChevronDownIcon className="w-5 h-5 text-gray-400" />
                                        )}
                                    </div>
                                </div>
                                
                                {expandedHistory[index] && (
                                    <div className="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                        <div className="mb-3">
                                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">Command:</h4>
                                            <div className="mt-1 bg-gray-800 rounded-md p-3 overflow-x-auto">
                                                <pre className="text-sm text-gray-200">
                                                    {getCommandString(item.command, item.arguments, item.options)}
                                                </pre>
                                            </div>
                                        </div>
                                        
                                        <div className="mb-3">
                                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">Output:</h4>
                                            <div className="mt-1">
                                                <SyntaxHighlighter 
                                                    language="bash" 
                                                    style={tomorrow}
                                                    customStyle={{ margin: 0, borderRadius: '0.375rem' }}
                                                >
                                                    {item.output || 'No output'}
                                                </SyntaxHighlighter>
                                            </div>
                                        </div>
                                        
                                        <div className="mt-3 flex justify-end">
                                            <button
                                                onClick={() => rerunCommand(item)}
                                                className="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                            >
                                                <PlayIcon className="w-4 h-4 mr-1" />
                                                Run Again
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
};

export default Artisan; 