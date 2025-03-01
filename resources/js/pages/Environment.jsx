import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { 
    ArrowPathIcon, 
    EyeIcon,
    EyeSlashIcon,
    MagnifyingGlassIcon,
    XMarkIcon,
    CheckCircleIcon,
    XCircleIcon
} from '@heroicons/react/24/outline';

const Environment = () => {
    const [variables, setVariables] = useState([]);
    const [filteredVariables, setFilteredVariables] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [searchTerm, setSearchTerm] = useState('');
    const [visibleSecrets, setVisibleSecrets] = useState({});
    const [clearingCache, setClearingCache] = useState(false);
    const [cacheMessage, setCacheMessage] = useState(null);

    useEffect(() => {
        fetchEnvironmentVariables();
    }, []);

    useEffect(() => {
        if (searchTerm) {
            const filtered = variables.filter(variable => 
                variable.key.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (variable.value && !variable.is_masked && variable.value.toLowerCase().includes(searchTerm.toLowerCase()))
            );
            setFilteredVariables(filtered);
        } else {
            setFilteredVariables(variables);
        }
    }, [searchTerm, variables]);

    const fetchEnvironmentVariables = async () => {
        try {
            setLoading(true);
            const response = await axios.get('/api/laravelops/env');
            setVariables(response.data);
            setError(null);
        } catch (err) {
            setError('Failed to load environment variables');
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const clearEnvCache = async () => {
        try {
            setClearingCache(true);
            setCacheMessage(null);
            
            const response = await axios.post('/api/laravelops/env/clear-cache');
            
            setCacheMessage({
                success: true,
                message: 'Environment cache cleared successfully'
            });
        } catch (err) {
            setCacheMessage({
                success: false,
                message: 'Failed to clear environment cache'
            });
            console.error(err);
        } finally {
            setClearingCache(false);
            
            // Auto-hide message after 5 seconds
            setTimeout(() => {
                setCacheMessage(null);
            }, 5000);
        }
    };

    const toggleSecretVisibility = (key) => {
        setVisibleSecrets(prev => ({
            ...prev,
            [key]: !prev[key]
        }));
    };

    const getVariableGroups = () => {
        const groups = {};
        
        filteredVariables.forEach(variable => {
            const parts = variable.key.split('_');
            const prefix = parts[0];
            
            if (!groups[prefix]) {
                groups[prefix] = [];
            }
            
            groups[prefix].push(variable);
        });
        
        return groups;
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center h-full">
                <div className="text-center">
                    <ArrowPathIcon className="w-12 h-12 mx-auto text-primary-500 animate-spin" />
                    <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">Loading environment variables...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <p className="text-red-600 dark:text-red-400">{error}</p>
                <button 
                    onClick={fetchEnvironmentVariables}
                    className="mt-2 px-4 py-2 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-md hover:bg-red-200 dark:hover:bg-red-700"
                >
                    Retry
                </button>
            </div>
        );
    }

    const variableGroups = getVariableGroups();

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Environment Variables</h1>
                <div className="flex space-x-3">
                    <button 
                        onClick={clearEnvCache}
                        disabled={clearingCache}
                        className="flex items-center px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        {clearingCache ? (
                            <ArrowPathIcon className="w-4 h-4 mr-2 animate-spin" />
                        ) : (
                            <XMarkIcon className="w-4 h-4 mr-2" />
                        )}
                        Clear Cache
                    </button>
                    <button 
                        onClick={fetchEnvironmentVariables}
                        className="flex items-center px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <ArrowPathIcon className="w-4 h-4 mr-2" />
                        Refresh
                    </button>
                </div>
            </div>
            
            {/* Cache Message */}
            {cacheMessage && (
                <div className={`p-4 rounded-md ${cacheMessage.success ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'}`}>
                    <div className="flex">
                        <div className="flex-shrink-0">
                            {cacheMessage.success ? (
                                <CheckCircleIcon className="h-5 w-5 text-green-400" />
                            ) : (
                                <XCircleIcon className="h-5 w-5 text-red-400" />
                            )}
                        </div>
                        <div className="ml-3">
                            <p className={`text-sm font-medium ${cacheMessage.success ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'}`}>
                                {cacheMessage.message}
                            </p>
                        </div>
                    </div>
                </div>
            )}
            
            {/* Search */}
            <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div className="relative">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon className="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        type="text"
                        placeholder="Search environment variables..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                    />
                </div>
                
                <div className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Showing {filteredVariables.length} of {variables.length} variables
                    {searchTerm && ' (filtered)'}
                </div>
            </div>
            
            {/* Variables */}
            {Object.keys(variableGroups).length === 0 ? (
                <div className="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg text-center">
                    <svg className="w-12 h-12 mx-auto text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 className="mt-4 text-lg font-medium text-yellow-800 dark:text-yellow-300">No Variables Found</h3>
                    <p className="mt-2 text-yellow-600 dark:text-yellow-400">
                        {searchTerm 
                            ? 'No variables match your search. Try a different search term.'
                            : 'No environment variables are available to view.'}
                    </p>
                </div>
            ) : (
                <div className="space-y-6">
                    {Object.entries(variableGroups).map(([group, vars]) => (
                        <div key={group} className="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                            <div className="px-4 py-3 bg-gray-50 dark:bg-gray-700">
                                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">{group}</h2>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Key
                                            </th>
                                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Value
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        {vars.map((variable) => (
                                            <tr key={variable.key} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {variable.key}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {variable.is_masked ? (
                                                        <div className="flex items-center">
                                                            {visibleSecrets[variable.key] ? (
                                                                <>
                                                                    <span className="mr-2">{variable.value}</span>
                                                                    <button 
                                                                        onClick={() => toggleSecretVisibility(variable.key)}
                                                                        className="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                                                                    >
                                                                        <EyeSlashIcon className="w-5 h-5" />
                                                                    </button>
                                                                </>
                                                            ) : (
                                                                <>
                                                                    <span className="mr-2">••••••••</span>
                                                                    <button 
                                                                        onClick={() => toggleSecretVisibility(variable.key)}
                                                                        className="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                                                                    >
                                                                        <EyeIcon className="w-5 h-5" />
                                                                    </button>
                                                                </>
                                                            )}
                                                        </div>
                                                    ) : (
                                                        variable.value
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

export default Environment; 