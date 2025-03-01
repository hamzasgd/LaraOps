import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import { 
    ArrowPathIcon, 
    PlayIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    ArrowUpOnSquareIcon,
    ArrowDownOnSquareIcon
} from '@heroicons/react/24/outline';
import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { tomorrow } from 'react-syntax-highlighter/dist/esm/styles/prism';

const Tinker = () => {
    const [code, setCode] = useState('');
    const [executing, setExecuting] = useState(false);
    const [result, setResult] = useState(null);
    const [history, setHistory] = useState([]);
    const [expandedHistory, setExpandedHistory] = useState({});
    const [showHistory, setShowHistory] = useState(false);
    const resultRef = useRef(null);
    const codeEditorRef = useRef(null);

    useEffect(() => {
        fetchHistory();
    }, []);

    useEffect(() => {
        if (result && resultRef.current) {
            resultRef.current.scrollIntoView({ behavior: 'smooth' });
        }
    }, [result]);

    const fetchHistory = async () => {
        try {
            const response = await axios.get('/api/laravelops/tinker/history');
            setHistory(response.data);
        } catch (err) {
            console.error('Failed to load tinker history', err);
        }
    };

    const executeTinker = async () => {
        if (!code.trim()) return;
        
        try {
            setExecuting(true);
            setResult(null);
            
            const response = await axios.post('/api/laravelops/tinker/execute', { code });
            setResult(response.data);
            
            // Save to history
            await axios.post('/api/laravelops/tinker/history', { 
                code, 
                output: response.data.output,
                success: response.data.success
            });
            
            // Refresh history
            fetchHistory();
        } catch (err) {
            setResult({
                success: false,
                output: err.response?.data?.message || 'An error occurred while executing the code'
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

    const loadFromHistory = (historyItem) => {
        setCode(historyItem.code);
        setResult(null);
        
        // Focus the code editor
        if (codeEditorRef.current) {
            codeEditorRef.current.focus();
        }
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleString();
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = e.target.selectionStart;
            const end = e.target.selectionEnd;
            
            // Insert 4 spaces at cursor position
            const newCode = code.substring(0, start) + '    ' + code.substring(end);
            setCode(newCode);
            
            // Move cursor after the inserted spaces
            setTimeout(() => {
                e.target.selectionStart = e.target.selectionEnd = start + 4;
            }, 0);
        } else if (e.key === 'Enter' && e.ctrlKey) {
            // Execute code with Ctrl+Enter
            executeTinker();
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Tinker</h1>
                <button 
                    onClick={() => setShowHistory(!showHistory)}
                    className="flex items-center px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    {showHistory ? (
                        <>
                            <ArrowUpOnSquareIcon className="w-4 h-4 mr-2" />
                            Hide History
                        </>
                    ) : (
                        <>
                            <ArrowDownOnSquareIcon className="w-4 h-4 mr-2" />
                            Show History
                        </>
                    )}
                </button>
            </div>
            
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Code Editor */}
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">PHP Code</h2>
                    <div className="mb-4">
                        <textarea
                            ref={codeEditorRef}
                            value={code}
                            onChange={(e) => setCode(e.target.value)}
                            onKeyDown={handleKeyDown}
                            placeholder="Enter PHP code to execute..."
                            className="w-full h-64 p-4 font-mono text-sm bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        ></textarea>
                        <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Press Tab to indent, Ctrl+Enter to execute
                        </p>
                    </div>
                    <div>
                        <button
                            onClick={executeTinker}
                            disabled={executing || !code.trim()}
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
                </div>
                
                {/* Result */}
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4" ref={resultRef}>
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Result</h2>
                    {result ? (
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
                                        {result.success ? 'Code executed successfully' : 'Code execution failed'}
                                    </h3>
                                </div>
                            </div>
                            <div className="mt-4">
                                <SyntaxHighlighter 
                                    language="php" 
                                    style={tomorrow}
                                    customStyle={{ margin: 0, borderRadius: '0.375rem', maxHeight: '300px', overflow: 'auto' }}
                                >
                                    {result.output || 'No output'}
                                </SyntaxHighlighter>
                            </div>
                        </div>
                    ) : (
                        <div className="text-center py-12">
                            <PlayIcon className="mx-auto h-12 w-12 text-gray-400" />
                            <h3 className="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No Result Yet</h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Execute some code to see the result here.
                            </p>
                        </div>
                    )}
                </div>
            </div>
            
            {/* History */}
            {showHistory && history.length > 0 && (
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Execution History</h2>
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
                                        <span className="text-sm font-medium text-gray-900 dark:text-gray-100 line-clamp-1">
                                            {item.code.substring(0, 50)}{item.code.length > 50 ? '...' : ''}
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
                                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">Code:</h4>
                                            <div className="mt-1">
                                                <SyntaxHighlighter 
                                                    language="php" 
                                                    style={tomorrow}
                                                    customStyle={{ margin: 0, borderRadius: '0.375rem' }}
                                                >
                                                    {item.code}
                                                </SyntaxHighlighter>
                                            </div>
                                        </div>
                                        
                                        <div className="mb-3">
                                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">Output:</h4>
                                            <div className="mt-1">
                                                <SyntaxHighlighter 
                                                    language="php" 
                                                    style={tomorrow}
                                                    customStyle={{ margin: 0, borderRadius: '0.375rem' }}
                                                >
                                                    {item.output || 'No output'}
                                                </SyntaxHighlighter>
                                            </div>
                                        </div>
                                        
                                        <div className="mt-3 flex justify-end">
                                            <button
                                                onClick={() => loadFromHistory(item)}
                                                className="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                            >
                                                <PlayIcon className="w-4 h-4 mr-1" />
                                                Load Code
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            )}
            
            {/* Tips */}
            <div className="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-md">
                <h3 className="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">Tips for using Tinker</h3>
                <ul className="list-disc pl-5 text-sm text-blue-700 dark:text-blue-400 space-y-1">
                    <li>Use <code className="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded">dump()</code> or <code className="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded">dd()</code> to output variables</li>
                    <li>Access models directly: <code className="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded">App\Models\User::first()</code></li>
                    <li>Use <code className="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded">DB::table(&apos;users&apos;)-&gt;get()</code> for database queries</li>
                    <li>Create variables: <code className="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded">$users = User::all()</code></li>
                    <li>Multiple lines of code are supported</li>
                </ul>
            </div>
        </div>
    );
};

export default Tinker; 