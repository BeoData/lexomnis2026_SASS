import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'LexOmnis Super Admin';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin);
        
        // Make route() helper available globally
        const routeHelper = (name, params = {}) => {
            if (typeof window !== 'undefined' && window.route) {
                return window.route(name, params);
            }
            
            // Fallback to direct URLs
            const routeMap = {
                'dashboard': '/dashboard',
                'tenants.index': '/tenants',
                'tenants.create': '/tenants/create',
                'tenants.show': (id) => `/tenants/${id}`,
                'tenants.edit': (id) => `/tenants/${id}/edit`,
                'tenants.store': '/tenants',
                'tenants.update': (id) => `/tenants/${id}`,
                'tenants.suspend': (id) => `/tenants/${id}/suspend`,
                'tenants.activate': (id) => `/tenants/${id}/activate`,
                'users.index': '/users',
                'users.show': (id) => `/users/${id}`,
                'users.suspend': (id) => `/users/${id}/suspend`,
                'users.reset-password': (id) => `/users/${id}/reset-password`,
                'users.impersonate': (id) => `/users/${id}/impersonate`,
                'plans.index': '/plans',
                'plans.create': '/plans/create',
                'plans.show': (id) => `/plans/${id}`,
                'plans.edit': (id) => `/plans/${id}/edit`,
                'plans.store': '/plans',
                'plans.update': (id) => `/plans/${id}`,
                'subscriptions.index': '/subscriptions',
                'subscriptions.show': (id) => `/subscriptions/${id}`,
                'system.index': '/system',
                'system.health': '/system/health',
                'system.metrics': '/system/metrics',
                'settings.index': '/settings',
                'settings.update': '/settings',
                'feature-flags.index': '/feature-flags',
                'feature-flags.create': '/feature-flags/create',
                'feature-flags.show': (id) => `/feature-flags/${id}`,
                'feature-flags.edit': (id) => `/feature-flags/${id}/edit`,
                'feature-flags.store': '/feature-flags',
                'feature-flags.update': (id) => `/feature-flags/${id}`,
                'audit-logs.index': '/audit-logs',
                'audit-logs.show': (id) => `/audit-logs/${id}`,
            };
            
            const routeHandler = routeMap[name];
            if (typeof routeHandler === 'function') {
                const id = params?.id || (typeof params === 'number' || typeof params === 'string' ? params : null);
                return routeHandler(id);
            }
            return routeHandler || '#';
        };
        
        app.config.globalProperties.route = routeHelper;
        app.provide('route', routeHelper);
        
        return app.mount(el);
    },
    progress: {
        color: '#2563eb',
    },
});
