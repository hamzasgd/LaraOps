import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import Layout from './components/Layout';
import AppRoutes from './routes';

const App = () => {
    return (
        <BrowserRouter basename="/laravelops">
            <Layout>
                <AppRoutes />
            </Layout>
        </BrowserRouter>
    );
};

export default App; 