import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
   broadcaster: 'pusher',
   key: 'your-pusher-key',
   cluster: 'eu',
   forceTLS: true
});

// Listen for notifications
window.Echo.channel('fulfillment-notifications')
    .listen('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (notification) => {
        addNotification(notification);
    });

function addNotification(notification) {
    const notificationsList = document.getElementById('notifications-list');
    const notificationItem = document.createElement('li');
    notificationItem.textContent = `New Order: ${notification.order_details}`;
    notificationsList.prepend(notificationItem);

    // Optional: Add a dismissable popup
    showPopupNotification(notification);
}

function showPopupNotification(notification) {
    const popup = document.createElement('div');
    popup.className = 'notification-popup';
    popup.textContent = `New Order: ${notification.order_details}`;
    document.body.appendChild(popup);

    setTimeout(() => {
        popup.remove();
    }, 5000);
}

function addNotification(notification) {
    const notificationsList = document.getElementById('notifications-list');
    const notificationItem = document.createElement('li');
    notificationItem.innerHTML = `New Order: ${notification.order_details} <button class="dismiss-btn">x</button>`;
    notificationsList.prepend(notificationItem);

    notificationItem.querySelector('.dismiss-btn').addEventListener('click', () => {
        notificationItem.remove();
    });

    showPopupNotification(notification);
}
