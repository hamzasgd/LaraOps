import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import { 
    ArrowPathIcon, 
    ArrowLeftIcon,
    ExclamationTriangleIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    FunnelIcon,
    XMarkIcon
} from '@heroicons/react/24/outline';
import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { tomorrow } from 'react-syntax-highlighter/dist/esm/styles/prism';

const LogDetail = () => {
    const { filename } = useParams();
    const [logs, setLogs] = useState([]);
    const [filteredLogs, setFilteredLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [filter, setFilter] = useState({
        level: '',
        search: ''
    });
    const [expandedEntries, setExpandedEntries] = useState({});
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const perPage = 50;

    useEffect(() => {
        fetchLogContent();
    }, [filename]);

    useEffect(() => {
        applyFilters();
    }, [logs, filter]);

    const fetchLogContent = async () => {
        try {
            setLoading(true);
            const response = await axios.get(`/api/laravelops/logs/${encodeURIComponent(filename)}`);
            setLogs(response.data);
            setError(null);
        } catch (err) {
            setError('Failed to load log content');
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const applyFilters = () => {
        let filtered = [...logs];
        
        if (filter.level) {
            filtered = filtered.filter(log => log.level === filter.level);
        }
        
        if (filter.search) {
            const searchTerm = filter.search.toLowerCase();
            filtered = filtered.filter(log => 
                log.message.toLowerCase().includes(searchTerm) || 
                (log.context && JSON.stringify(log.context).toLowerCase().includes(searchTerm))
            );
        }
        
        setFilteredLogs(filtered);
        setPage(1);
        setHasMore(filtered.length > perPage);
    };

    const handleFilterChange = (key, value) => {
        setFilter(prev => ({
            ...prev,
            [key]: value
        }));
    };

    const clearFilters = () => {
        setFilter({
            level: '',
            search: ''
        });
    };

    const toggleExpand = (id) => {
        setExpandedEntries(prev => ({
            ...prev,
            [id]: !prev[id]
        }));
    };

    const loadMore = () => {
        setPage(prev => prev + 1);
        setHasMore(filteredLogs.length > page * perPage + perPage);
    };

    const getLevelColor = (level) => {
        switch (level?.toLowerCase()) {
            case 'emergency':
            case 'alert':
            case 'critical':
            case 'error':
                return 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20';
            case 'warning':
                return 'text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20';
            case 'notice':
            case 'info':
                return 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20';
            case 'debug':
                return 'text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50';
            default:
                return 'text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50';
        }
    };

    const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleString();
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center h-full">
                <div className="text-center">
                    <ArrowPathIcon className="w-12 h-12 mx-auto text-primary-500 animate-spin" />
                    <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">Loading log content...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <p className="text-red-600 dark:text-red-400">{error}</p>
                <button 
                    onClick={fetchLogContent}
                    className="mt-2 px-4 py-2 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-md hover:bg-red-200 dark:hover:bg-red-700"
                >
                    Retry
                </button>
            </div>
        );
    }

    const displayedLogs = filteredLogs.slice(0, page * perPage);
    const uniqueLevels = [...new Set(logs.map(log => log.level))].filter(Boolean);

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div className="flex items-center">
                    <Link 
                        to="/laravelops/logs" 
                        className="mr-4 p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        <ArrowLeftIcon className="w-5 h-5" />
                    </Link>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                        {decodeURIComponent(filename)}
                    </h1>
                </div>
                <button 
                    onClick={fetchLogContent}
                    className="flex items-center px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <ArrowPathIcon className="w-4 h-4 mr-2" />
                    Refresh
                </button>
            </div>
            
            {/* Filters */}
            <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div className="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                    <div className="flex items-center">
                        <FunnelIcon className="w-5 h-5 text-gray-400 mr-2" />
                        <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Filters:</span>
                    </div>
                    
                    <div className="flex-1 flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                        <div className="w-full md:w-48">
                            <label htmlFor="level-filter" className="sr-only">Filter by level</label>
                            <select
                                id="level-filter"
                                value={filter.level}
                                onChange={(e) => handleFilterChange('level', e.target.value)}
                                className="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Levels</option>
                                {uniqueLevels.map(level => (
                                    <option key={level} value={level}>{level}</option>
                                ))}
                            </select>
                        </div>
                        
                        <div className="flex-1">
                            <label htmlFor="search-filter" className="sr-only">Search logs</label>
                            <input
                                id="search-filter"
                                type="text"
                                placeholder="Search in logs..."
                                value={filter.search}
                                onChange={(e) => handleFilterChange('search', e.target.value)}
                                className="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                        
                        {(filter.level || filter.search) && (
                            <button
                                onClick={clearFilters}
                                className="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                <XMarkIcon className="w-4 h-4 mr-1" />
                                Clear
                            </button>
                        )}
                    </div>
                </div>
                
                <div className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Showing {displayedLogs.length} of {filteredLogs.length} entries
                    {filter.level || filter.search ? ' (filtered)' : ''}
                </div>
            </div>
            
            {/* Log Entries */}
            {filteredLogs.length === 0 ? (
                <div className="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg text-center">
                    <ExclamationTriangleIcon className="w-12 h-12 mx-auto text-yellow-500 dark:text-yellow-400" />
                    <h3 className="mt-4 text-lg font-medium text-yellow-800 dark:text-yellow-300">No Log Entries Found</h3>
                    <p className="mt-2 text-yellow-600 dark:text-yellow-400">
                        {filter.level || filter.search 
                            ? 'No entries match your current filters. Try adjusting or clearing your filters.'
                            : 'This log file is empty.'}
                    </p>
                </div>
            ) : (
                <div className="space-y-4">
                    {displayedLogs.map((log, index) => (
                        <div 
                            key={index} 
                            className="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden"
                        >
                            <div 
                                className="px-4 py-3 cursor-pointer flex items-center justify-between"
                                onClick={() => toggleExpand(index)}
                            >
                                <div className="flex items-center space-x-3">
                                    {log.level && (
                                        <span className={`px-2 py-1 rounded-md text-xs font-medium ${getLevelColor(log.level)}`}>
                                            {log.level}
                                        </span>
                                    )}
                                    <span className="text-sm text-gray-500 dark:text-gray-400">
                                        {formatDate(log.datetime)}
                                    </span>
                                </div>
                                <div className="flex items-center">
                                    <span className="text-sm font-medium text-gray-900 dark:text-gray-100 mr-2 line-clamp-1">
                                        {log.message}
                                    </span>
                                    {expandedEntries[index] ? (
                                        <ChevronUpIcon className="w-5 h-5 text-gray-400" />
                                    ) : (
                                        <ChevronDownIcon className="w-5 h-5 text-gray-400" />
                                    )}
                                </div>
                            </div>
                            
                            {expandedEntries[index] && (
                                <div className="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                    <div className="mb-2">
                                        <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">Message:</h4>
                                        <p className="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                            {log.message}
                                        </p>
                                    </div>
                                    
                                    {log.context && Object.keys(log.context).length > 0 && (
                                        <div className="mt-4">
                                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">Context:</h4>
                                            <div className="mt-1 rounded-md overflow-hidden">
                                                <SyntaxHighlighter 
                                                    language="json" 
                                                    style={tomorrow}
                                                    customStyle={{ margin: 0, borderRadius: '0.375rem' }}
                                                >
                                                    {JSON.stringify(log.context, null, 2)}
                                                </SyntaxHighlighter>
                                            </div>
                                        </div>
                                    )}
                                    
                                    {log.stack && (
                                        <div className="mt-4">
                                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">Stack Trace:</h4>
                                            <div className="mt-1 rounded-md overflow-hidden">
                                                <SyntaxHighlighter 
                                                    language="php" 
                                                    style={tomorrow}
                                                    customStyle={{ margin: 0, borderRadius: '0.375rem' }}
                                                >
                                                    {log.stack}
                                                </SyntaxHighlighter>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                    
                    {hasMore && (
                        <div className="text-center">
                            <button
                                onClick={loadMore}
                                className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                Load More
                            </button>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default LogDetail; 