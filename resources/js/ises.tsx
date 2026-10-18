import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType } from 'react';
import { createRoot } from 'react-dom/client';
import '../css/ises.css';

const pages = import.meta.glob<{ default: ComponentType }>('./ises/pages/**/*.tsx');

createInertiaApp({
    title: (title) => (title ? title + ' · ISES' : 'ISES · Survey Estimation'),
    resolve: async (name) => {
        const loader = pages['./ises/pages/' + name + '.tsx'];
        if (!loader) throw new Error('Unknown ISES page: ' + name);
        return (await loader()).default;
    },
    setup({ el, App, props }) {
        if (!el) throw new Error('Inertia root element was not found.');
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#1d5b78',
    },
});
