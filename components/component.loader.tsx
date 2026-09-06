
import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { CssBaseline, ThemeProvider } from '@mui/material';
import { theme } from './theme';

interface ComponentCallback {
    (element: HTMLElement, parameters: any) : void;
}

// Contains a list of React components that are available to be rendered.
const componentMap = new Map<string, ComponentCallback>();

// Add React component to Map
export const registerComponent = (id: string, callback: ComponentCallback) => {
    componentMap.set(id, callback);
}

// Mounts a component's root node wrapped in the shared MUI theme.
export const renderWithTheme = (element: HTMLElement, node: React.ReactNode) => {
    ReactDOMClient.createRoot(element).render(
        <ThemeProvider theme={theme}>
            <CssBaseline />
            {node}
        </ThemeProvider>
    );
}

// Watches dom for elements that call for react component.
document.addEventListener('DOMContentLoaded', () => {

    const targetElements = document.body.querySelectorAll('.react-component');

    targetElements.forEach( element => {

        const component = element.attributes.getNamedItem('data-component').value;
        const data = element.attributes.getNamedItem('data-parameters').value;

        let parameters :  string | null = null;
        if (data.length > 0){
            parameters = JSON.parse(window.atob(data)) ?? null;
        }
        
        const componentCallback = componentMap.get(component);
        if (componentCallback) {
            componentCallback(element as HTMLElement, parameters as any);
        } else {
            console.log('Unknown component request: ' + component);
        }

        element.classList.remove('react-component');
        element.classList.add('loaded');
    });
});