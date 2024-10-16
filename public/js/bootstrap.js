import axios from 'axios';
// Import Pusher and Echo
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';


window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Set up Laravel Echo with Pusher
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '98996ab613163534e588', // Pusher Key
    cluster: 'ap1', // Your Pusher cluster
    encrypted: true, // Enable SSL/TLS
    forceTLS: true // Optional for SSL
});
