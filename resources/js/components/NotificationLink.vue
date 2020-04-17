<template>
    <div>
        <a
                v-if="external"
                @click.native="$emit('click')"
                :href="href"
                target="_blank"
                :class="style"
        >
            {{ slot }}
        </a>
        <router-link
                v-else
                @click.native="$emit('click')"
                :to="href"
                :class="style"
        >
            {{ slot }}
        </router-link>
    </div>
</template>

<script>
    export default {
        props: {
            external: {
                type: Boolean,
                default: false
            },
            href: {
                type: String
            },
            style: {
                type: String
            }
        },
    }
</script>