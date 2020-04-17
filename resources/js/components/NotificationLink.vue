<template>
    <div>
        <a
                v-if="external"
                @click="$emit('click')"
                :href="href"
                target="_blank"
                :class="classList"
        >
            <slot></slot>
        </a>
        <router-link
                v-else
                @click.native="$emit('click')"
                :to="href"
                :class="classList"
        >
            <slot></slot>
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
            classList: {
                type: Array
            }
        },
    }
</script>