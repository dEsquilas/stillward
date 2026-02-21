import axios from 'axios';
import { router } from '@inertiajs/vue3';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Refresh CSRF token by hitting Sanctum's csrf-cookie endpoint.
 */
async function refreshCsrfToken() {
    await axios.get('/sanctum/csrf-cookie');
}

/**
 * When the app returns to the foreground (mobile tab switch, screen unlock, etc.),
 * silently refresh the session and CSRF cookie to prevent 419 errors.
 */
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        refreshCsrfToken().catch(() => {});
    }
});

/**
 * Axios interceptor: on a 419 (CSRF token mismatch), refresh the token
 * and retry the original request once.
 */
axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if (error.response?.status === 419 && !originalRequest._retried) {
            originalRequest._retried = true;

            await refreshCsrfToken();

            return axios(originalRequest);
        }

        return Promise.reject(error);
    },
);
