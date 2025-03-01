import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { 
    ArrowPathIcon, 
    PlayIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    CalendarIcon,
    InformationCircleIcon
} from '@heroicons/react/24/outline';

const Schedule = () => {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [runningTask, setRunningTask] = useState(null);
    const [taskResults, setTaskResults] = useState({});
    const [expandedInfo, setExpandedInfo] = useState({});

    useEffect(() => {
        fetchTasks();
    }, []);

    const fetchTasks = async () => {
        try {
            setLoading(true);
            setError(null);
            const response = await axios.get('/api/laravelops/schedule');
            setTasks(response.data);
        } catch (err) {
            setError('Failed to load scheduled tasks. Please try again.');
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const runTask = async (task) => {
        try {
            setRunningTask(task.command);
            setTaskResults(prev => ({
                ...prev,
                [task.command]: { loading: true }
            }));
            
            const response = await axios.post('/api/laravelops/schedule/run', {
                command: task.command
            });
            
            setTaskResults(prev => ({
                ...prev,
                [task.command]: {
                    loading: false,
                    success: response.data.success,
                    output: response.data.output,
                    timestamp: new Date()
                }
            }));
        } catch (err) {
            setTaskResults(prev => ({
                ...prev,
                [task.command]: {
                    loading: false,
                    success: false,
                    output: err.response?.data?.message || 'An error occurred while running the task',
                    timestamp: new Date()
                }
            }));
            console.error(err);
        } finally {
            setRunningTask(null);
        }
    };

    const toggleInfo = (command) => {
        setExpandedInfo(prev => ({
            ...prev,
            [command]: !prev[command]
        }));
    };

    const formatNextDue = (expression) => {
        try {
            // This is a simplified version - in a real app, you'd use a cron parser library
            return 'Next due based on: ' + expression;
        } catch (err) {
            return 'Unable to determine next run time';
        }
    };

    const formatDate = (date) => {
        return new Date(date).toLocaleString();
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <ArrowPathIcon className="w-8 h-8 text-primary-500 animate-spin" />
                <span className="ml-2 text-gray-700 dark:text-gray-300">Loading scheduled tasks...</span>
            </div>
        );
    }

    if (error) {
        return (
            <div className="bg-red-50 dark:bg-red-900/20 p-4 rounded-md">
                <div className="flex">
                    <div className="flex-shrink-0">
                        <XCircleIcon className="h-5 w-5 text-red-400" />
                    </div>
                    <div className="ml-3">
                        <h3 className="text-sm font-medium text-red-800 dark:text-red-300">
                            Error Loading Tasks
                        </h3>
                        <div className="mt-2 text-sm text-red-700 dark:text-red-400">
                            <p>{error}</p>
                        </div>
                        <div className="mt-4">
                            <button
                                type="button"
                                onClick={fetchTasks}
                                className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <ArrowPathIcon className="w-4 h-4 mr-2" />
                                Retry
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Scheduled Tasks</h1>
                <button 
                    onClick={fetchTasks}
                    className="flex items-center px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <ArrowPathIcon className="w-4 h-4 mr-2" />
                    Refresh
                </button>
            </div>
            
            {tasks.length === 0 ? (
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <CalendarIcon className="mx-auto h-12 w-12 text-gray-400" />
                    <h3 className="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No scheduled tasks</h3>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        No scheduled tasks were found in your application.
                    </p>
                </div>
            ) : (
                <div className="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead className="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Description
                                </th>
                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Schedule
                                </th>
                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Command
                                </th>
                                <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {tasks.map((task, index) => (
                                <React.Fragment key={index}>
                                    <tr className={index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700/30'}>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {task.description || 'No description'}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {task.expression}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <code className="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">
                                                {task.command}
                                            </code>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div className="flex justify-end space-x-2">
                                                <button
                                                    onClick={() => toggleInfo(task.command)}
                                                    className="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                                                >
                                                    <InformationCircleIcon className="w-5 h-5" />
                                                </button>
                                                <button
                                                    onClick={() => runTask(task)}
                                                    disabled={runningTask === task.command}
                                                    className="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    {runningTask === task.command ? (
                                                        <ArrowPathIcon className="w-5 h-5 animate-spin" />
                                                    ) : (
                                                        <PlayIcon className="w-5 h-5" />
                                                    )}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    {/* Expanded Info Row */}
                                    {expandedInfo[task.command] && (
                                        <tr className="bg-gray-50 dark:bg-gray-700/20">
                                            <td colSpan="4" className="px-6 py-4">
                                                <div className="text-sm text-gray-700 dark:text-gray-300 space-y-3">
                                                    <div>
                                                        <span className="font-medium">Next Due:</span> {formatNextDue(task.expression)}
                                                    </div>
                                                    {task.timezone && (
                                                        <div>
                                                            <span className="font-medium">Timezone:</span> {task.timezone}
                                                        </div>
                                                    )}
                                                    {task.withoutOverlapping && (
                                                        <div>
                                                            <span className="font-medium">Without Overlapping:</span> Yes
                                                        </div>
                                                    )}
                                                    {task.onOneServer && (
                                                        <div>
                                                            <span className="font-medium">On One Server:</span> Yes
                                                        </div>
                                                    )}
                                                    {task.inBackground && (
                                                        <div>
                                                            <span className="font-medium">In Background:</span> Yes
                                                        </div>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                    
                                    {/* Task Result Row */}
                                    {taskResults[task.command] && (
                                        <tr className={taskResults[task.command].success ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'}>
                                            <td colSpan="4" className="px-6 py-4">
                                                <div className="flex">
                                                    <div className="flex-shrink-0">
                                                        {taskResults[task.command].success ? (
                                                            <CheckCircleIcon className="h-5 w-5 text-green-400" />
                                                        ) : (
                                                            <XCircleIcon className="h-5 w-5 text-red-400" />
                                                        )}
                                                    </div>
                                                    <div className="ml-3 flex-1">
                                                        <div className="flex justify-between">
                                                            <h3 className={`text-sm font-medium ${taskResults[task.command].success ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'}`}>
                                                                {taskResults[task.command].success ? 'Task executed successfully' : 'Task execution failed'}
                                                            </h3>
                                                            {taskResults[task.command].timestamp && (
                                                                <span className="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                                                    <ClockIcon className="w-4 h-4 mr-1" />
                                                                    {formatDate(taskResults[task.command].timestamp)}
                                                                </span>
                                                            )}
                                                        </div>
                                                        <div className="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                                            <pre className="whitespace-pre-wrap bg-gray-100 dark:bg-gray-700 p-3 rounded-md text-xs overflow-auto max-h-40">
                                                                {taskResults[task.command].output || 'No output'}
                                                            </pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                </React.Fragment>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
            
            <div className="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-md">
                <h3 className="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">About Scheduled Tasks</h3>
                <p className="text-sm text-blue-700 dark:text-blue-400">
                    This page displays all scheduled tasks defined in your Laravel application's Console\Kernel.php file. 
                    You can run tasks manually by clicking the play button. Note that some tasks may take a long time to complete.
                </p>
            </div>
        </div>
    );
};

export default Schedule; 