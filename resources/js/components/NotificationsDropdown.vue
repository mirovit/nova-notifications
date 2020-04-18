<template>
    <dropdown class="ml-auto h-9 flex items-center dropdown-right">
        <dropdown-trigger class="h-9 flex items-center">
        <span class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="ml-2 text-90 bg-gray-800 w-8">
                <path
                    d="M15 19a3 3 0 0 1-6 0H4a1 1 0 0 1 0-2h1v-6a7 7 0 0 1 4.02-6.34 3 3 0 0 1 5.96 0A7 7 0 0 1 19 11v6h1a1 1 0 0 1 0 2h-5zm-4 0a1 1 0 0 0 2 0h-2zm0-12.9A5 5 0 0 0 7 11v6h10v-6a5 5 0 0 0-4-4.9V5a1 1 0 0 0-2 0v1.1z"/>
            </svg>
            <span
                class="absolute -mt-4 -mr-1 text-xs bg-danger text-danger-light text-sm font-bold px-1 shadow-lg rounded-lg"
                style="right: 0;"
                v-if="count > 0">
                <span v-if="count < 9">{{ count }}</span>
                <span v-else>9+</span>
            </span>
        </span>
        </dropdown-trigger>

        <dropdown-menu slot="menu" width="600" direction="rtl">
            <loading-view :loading="isLoading">
                <div class="flex justify-between bg-40 text-90 p-4">
                    <h3>{{ __('Notifications') }}</h3>

                    <button v-if="count !== 0" class="btn" @click="markAllAsRead()">
                        {{ __('mark all as read') }}
                    </button>
                </div>
                <p v-if="count === 0" class="block p-3">
                    {{ __('No new notifications') }}
                </p>
                <scroll-wrap v-else height="350">
                    <slot>
                        <notification-item
                            :ref="'notification-' + notification.id"
                            v-for="notification in notifications"
                            :key="notification.id"
                            :notification="notification"
                        >
                        </notification-item>
                    </slot>
                </scroll-wrap>
            </loading-view>
        </dropdown-menu>
    </dropdown>
</template>

<script>
    export default {
        data() {
            return {
                count: 0,
                notifications: [],
                isLoading: true,
            }
        },
        mounted() {
            const self = this

            self.loadNotifications(function (response) {
                Echo.private(Nova.config.user_model_namespace + '.' + response.data.user_id)
                    .notification(self.notificationReceived)
            })

            Nova.$on('notification-read', function (e) {
                self.notifications[e.notification.id] = e.notification
                self.count--;
            })
        },
        methods: {
            loadNotifications: function (callback) {
                axios
                    .get('/nova-vendor/nova-notifications/unread')
                    .then(response => {
                        this.isLoading = false
                        this.count = response.data.count
                        this.notifications = response.data.notifications

                        if (callback) {
                            callback(response)
                        }
                    })
            },
            notificationReceived: function (notification) {
                const self = this

                self.loadNotifications()

                let level = 'info'
                const levels = ['success', 'info', 'error']

                if (levels.indexOf(notification.level) !== -1) {
                    level = notification.level
                }

                const markAsRead = {
                    text: self.__('Mark as Read'),
                    onClick: (e, toast) => {
                        self.$refs['notification-' + notification.id][0].markAsRead()
                        toast.goAway(0);
                    }
                }

                const cancel = {
                    text: self.__('Cancel'),
                    onClick: (e, toast) => {
                        toast.goAway(0);
                    }
                }

                let actions = []

                if (notification.show_mark_as_read) {
                    actions.push(markAsRead)
                }

                if (notification.show_cancel) {
                    actions.push(cancel)
                }

                self.$toasted.show(notification.title, {
                    type: level,
                    keepOnHover: true,
                    icon: notification.icon || null,
                    iconPack: 'custom-class',
                    action: actions,
                })
            },
            markAllAsRead: function () {
                axios
                    .patch('/nova-vendor/nova-notifications')
                    .then(() => {
                        this.notifications = []
                        this.count = 0
                    })
            }
        }
    }
</script>

<style scoped>
    #max-height {
        max-height: 30rem;
    }

    .bg-light-gray {
        background-color: #EEEEEE;
    }

    .bg-light-gray:hover {
        background-color: #fefefe;
    }
</style>
