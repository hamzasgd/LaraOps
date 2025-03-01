import React from 'react';
import { Routes, Route } from 'react-router-dom';
import Dashboard from './pages/Dashboard';
import Logs from './pages/Logs';
import LogDetail from './pages/LogDetail';
import Artisan from './pages/Artisan';
import Environment from './pages/Environment';
import Tinker from './pages/Tinker';
import Schedule from './pages/Schedule';
import NotFound from './pages/NotFound';

const AppRoutes = () => {
    return (
        <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/logs" element={<Logs />} />
            <Route path="/logs/:filename" element={<LogDetail />} />
            <Route path="/artisan" element={<Artisan />} />
            <Route path="/environment" element={<Environment />} />
            <Route path="/tinker" element={<Tinker />} />
            <Route path="/schedule" element={<Schedule />} />
            <Route path="*" element={<NotFound />} />
        </Routes>
    );
};

export default AppRoutes; 