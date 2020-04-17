Nova.booting((Vue, router, store) => {
    Vue.component('notification-link', require('./components/NotificationLink'))
    Vue.component('notification-item', require('./components/NotificationItem'))
    Vue.component('notifications-dropdown', require('./components/NotificationsDropdown'))
})
