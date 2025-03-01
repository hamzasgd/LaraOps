import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';

// Get the app container
const container = document.getElementById('app');

// Create a root
if (container) {
    const root = createRoot(container);
    
    // Render the app
    root.render(
        <BrowserRouter>
            <App />
        </BrowserRouter>
    );
} 