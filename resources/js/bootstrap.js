import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Set CSRF token header for Laravel
try {
	const tokenMeta = document.querySelector('meta[name="csrf-token"]');
	if (tokenMeta) {
		window.axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
	}
} catch (e) {
	console.debug('No CSRF token meta found or error reading it', e);
}

// Ensure cookies are sent (useful if dev server proxies or different host)
window.axios.defaults.withCredentials = true;
