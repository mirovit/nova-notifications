<template>
    <div class="block no-underline text-90 hover:bg-30 p-3">
        <div class="flex justify-between">
            <router-link
                @click.native="markAsRead()"
                :to="formatRouterLink(notification)"
                class="no-underline text-black flex-1"
            >
                <slot name="content">
                    <h4 class="no-underline dim text-primary font-bold pb-2">{{ notification.data.title }}</h4>

                    <p
                        v-if="notification.data.subtitle"
                        class="pb-2 font-normal"
                    >
                        {{ notification.data.subtitle }}
                    </p>

                    <p class="font-normal text-sm">
                        {{ notification.data.created_at | fromNow }}
                    </p>
                </slot>
            </router-link>

            <button
                v-if="notification.read_at === null"
                @click="markAsRead()"
                class="btn"
            >
                <slot name="action">
                    mark read
                </slot>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            notification: {
                type: Object
            }
        },
        filters: {
            fromNow(date) {
                return new moment(date).fromNow()
            }
        },
        methods: {
            markAsRead: function () {
                axios
                    .patch('/nova-vendor/nova-notifications/' + this.notification.id)
                    .then(response => {
                        if (response.data.notification.read_at) {
                            Nova.$emit('notification-read', {notification: response.data.notification})
                        }
                    })
            },
            formatRouterLink: function (notification) {
                if (notification.data.router) {
                    return {name: notification.data.route.name, params: notification.data.route.params}
                }

                return {path: notification.data.url || '#'}
            }
        }
    }
</script>
