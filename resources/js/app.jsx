import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import AppComponent from './AppComponent';

// Get the app container
const container = document.getElementById('app');

// Create a root
if (container) {
    const root = createRoot(container);
    
    // Render the app
    root.render(
        <React.StrictMode>
            <AppComponent />
        </React.StrictMode>
    );
} 