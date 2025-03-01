import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { 
    ArrowPathIcon, 
    ServerIcon, 
    CpuChipIcon, 
    CircleStackIcon 
} from '@heroicons/react/24/outline';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Title } from 'chart.js';
import { Doughnut, Line } from 'react-chartjs-2';

// Register ChartJS components
ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Title);

const Dashboard = () => {
    const [systemInfo, setSystemInfo] = useState({
        php_version: '',
        laravel_version: '',
        database: {
            connection: '',
            version: ''
        },
        server: {
            software: '',
            os: ''
        },
        memory_usage: 0,
        cpu_usage: 0,
        disk_usage: {
            used: 0,
            total: 0
        }
    });
    
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [resourceHistory, setResourceHistory] = useState({
        labels: [],
        cpu: [],
        memory: []
    });

    useEffect(() => {
        fetchSystemInfo();
        const interval = setInterval(fetchResourceUsage, 5000);
        
        return () => clearInterval(interval);
    }, []);

    const fetchSystemInfo = async () => {
        try {
            setLoading(true);
            const response = await axios.get('/api/laravelops/system');
            setSystemInfo(response.data);
            setError(null);
        } catch (err) {
            setError('Failed to load system information');
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const fetchResourceUsage = async () => {
        try {
            const response = await axios.get('/api/laravelops/system/resources');
            
            setSystemInfo(prev => ({
                ...prev,
                memory_usage: response.data.memory_usage,
                cpu_usage: response.data.cpu_usage,
                disk_usage: response.data.disk_usage
            }));
            
            // Update history
            const now = new Date().toLocaleTimeString();
            setResourceHistory(prev => {
                const labels = [...prev.labels, now].slice(-10);
                const cpu = [...prev.cpu, response.data.cpu_usage].slice(-10);
                const memory = [...prev.memory, response.data.memory_usage].slice(-10);
                
                return { labels, cpu, memory };
            });
        } catch (err) {
            console.error('Failed to fetch resource usage', err);
        }
    };

    const handleClearCache = async () => {
        try {
            await axios.post('/api/laravelops/system/clear-cache');
            alert('Cache cleared successfully');
        } catch (err) {
            alert('Failed to clear cache');
            console.error(err);
        }
    };

    const handleClearViews = async () => {
        try {
            await axios.post('/api/laravelops/system/clear-views');
            alert('View cache cleared successfully');
        } catch (err) {
            alert('Failed to clear view cache');
            console.error(err);
        }
    };

    const handleClearRoutes = async () => {
        try {
            await axios.post('/api/laravelops/system/clear-routes');
            alert('Route cache cleared successfully');
        } catch (err) {
            alert('Failed to clear route cache');
            console.error(err);
        }
    };

    const handleCreateStorageLink = async () => {
        try {
            await axios.post('/api/laravelops/system/create-link');
            alert('Storage link created successfully');
        } catch (err) {
            alert('Failed to create storage link');
            console.error(err);
        }
    };

    // Prepare chart data
    const diskData = {
        labels: ['Used', 'Free'],
        datasets: [
            {
                data: [
                    systemInfo.disk_usage.used,
                    systemInfo.disk_usage.total - systemInfo.disk_usage.used
                ],
                backgroundColor: ['#0ea5e9', '#e0f2fe'],
                borderColor: ['#0284c7', '#bae6fd'],
                borderWidth: 1,
            },
        ],
    };

    const resourceData = {
        labels: resourceHistory.labels,
        datasets: [
            {
                label: 'CPU Usage (%)',
                data: resourceHistory.cpu,
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14, 165, 233, 0.2)',
                tension: 0.4,
            },
            {
                label: 'Memory Usage (%)',
                data: resourceHistory.memory,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                tension: 0.4,
            },
        ],
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center h-full">
                <div className="text-center">
                    <ArrowPathIcon className="w-12 h-12 mx-auto text-primary-500 animate-spin" />
                    <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">Loading system information...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <p className="text-red-600 dark:text-red-400">{error}</p>
                <button 
                    onClick={fetchSystemInfo}
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
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">System Dashboard</h1>
                <button 
                    onClick={fetchSystemInfo}
                    className="flex items-center px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <ArrowPathIcon className="w-4 h-4 mr-2" />
                    Refresh
                </button>
            </div>
            
            {/* System Info Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 card-hover">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Version</p>
                            <p className="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{systemInfo.php_version}</p>
                        </div>
                        <div className="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                            <ServerIcon className="w-6 h-6 text-blue-500 dark:text-blue-400" />
                        </div>
                    </div>
                </div>
                
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 card-hover">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Version</p>
                            <p className="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{systemInfo.laravel_version}</p>
                        </div>
                        <div className="p-2 bg-red-50 dark:bg-red-900/20 rounded-md">
                            <svg className="w-6 h-6 text-red-500 dark:text-red-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21.4 7.5c.8.8.8 2.1 0 2.8l-9 9c-.8.8-2.1.8-2.8 0l-9-9c-.8-.8-.8-2.1 0-2.8l9-9c.8-.8 2.1-.8 2.8 0l9 9z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 card-hover">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Database</p>
                            <p className="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{systemInfo.database.connection}</p>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{systemInfo.database.version}</p>
                        </div>
                        <div className="p-2 bg-green-50 dark:bg-green-900/20 rounded-md">
                            <CircleStackIcon className="w-6 h-6 text-green-500 dark:text-green-400" />
                        </div>
                    </div>
                </div>
                
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 card-hover">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Server</p>
                            <p className="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{systemInfo.server.software}</p>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{systemInfo.server.os}</p>
                        </div>
                        <div className="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-md">
                            <CpuChipIcon className="w-6 h-6 text-purple-500 dark:text-purple-400" />
                        </div>
                    </div>
                </div>
            </div>
            
            {/* Resource Usage */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 lg:col-span-2">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Resource Usage History</h2>
                    <div className="h-64">
                        <Line 
                            data={resourceData} 
                            options={{
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        ticks: {
                                            callback: value => `${value}%`
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                }
                            }} 
                        />
                    </div>
                </div>
                
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Disk Usage</h2>
                    <div className="h-64 flex items-center justify-center">
                        <Doughnut 
                            data={diskData} 
                            options={{
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.raw || 0;
                                                const total = systemInfo.disk_usage.total;
                                                const percentage = Math.round((value / total) * 100);
                                                return `${label}: ${(value / 1024).toFixed(2)} GB (${percentage}%)`;
                                            }
                                        }
                                    }
                                }
                            }} 
                        />
                    </div>
                    <div className="mt-4 text-center">
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            {(systemInfo.disk_usage.used / 1024).toFixed(2)} GB used of {(systemInfo.disk_usage.total / 1024).toFixed(2)} GB
                        </p>
                    </div>
                </div>
            </div>
            
            {/* Quick Actions */}
            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button 
                        onClick={handleClearCache}
                        className="px-4 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-md hover:bg-primary-100 dark:hover:bg-primary-800/20"
                    >
                        Clear Cache
                    </button>
                    <button 
                        onClick={handleClearViews}
                        className="px-4 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-md hover:bg-primary-100 dark:hover:bg-primary-800/20"
                    >
                        Clear View Cache
                    </button>
                    <button 
                        onClick={handleClearRoutes}
                        className="px-4 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-md hover:bg-primary-100 dark:hover:bg-primary-800/20"
                    >
                        Clear Route Cache
                    </button>
                    <button 
                        onClick={handleCreateStorageLink}
                        className="px-4 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-md hover:bg-primary-100 dark:hover:bg-primary-800/20"
                    >
                        Create Storage Link
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Dashboard; 