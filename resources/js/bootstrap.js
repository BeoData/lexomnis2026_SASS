import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token setup and auto-refresh
const updateCsrfToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    } else {
        console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
    }
};

// Initial setup
updateCsrfToken();

// Auto-refresh CSRF token on every response to prevent 419 errors
axios.interceptors.response.use(
    (response) => {
        // Update CSRF token from response headers if available
        const newToken = response.headers['x-csrf-token'];
        if (newToken) {
            const metaToken = document.head.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                metaToken.setAttribute('content', newToken);
            }
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
        }
        return response;
    },
    (error) => {
        // If we get a 419 error, try to refresh the token and retry
        if (error.response && error.response.status === 419) {
            // Update token from response if available
            const newToken = error.response.headers['x-csrf-token'];
            if (newToken) {
                const metaToken = document.head.querySelector('meta[name="csrf-token"]');
                if (metaToken) {
                    metaToken.setAttribute('content', newToken);
                }
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                
                // Retry the original request
                const config = error.config;
                if (config && !config._retry) {
                    config._retry = true;
                    return axios(config);
                }
            }
        }
        return Promise.reject(error);
    }
);
