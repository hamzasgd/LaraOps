import React from 'react';
import { Link } from 'react-router-dom';
import { HomeIcon } from '@heroicons/react/24/outline';

const NotFound = () => {
    return (
        <div className="flex flex-col items-center justify-center min-h-[70vh] py-12">
            <div className="text-center">
                <h1 className="text-9xl font-bold text-primary-600 dark:text-primary-400">404</h1>
                <h2 className="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Page Not Found</h2>
                <p className="mt-6 text-base text-gray-600 dark:text-gray-400">
                    Sorry, we couldn't find the page you're looking for.
                </p>
                <div className="mt-10">
                    <Link
                        to="/"
                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        <HomeIcon className="w-5 h-5 mr-2" />
                        Back to Dashboard
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default NotFound; 