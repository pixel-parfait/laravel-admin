<script setup>
import Modal from '@/Components/Modal.vue'
</script>

<template>
    <main>
        <slot />
    </main>

    <modal v-if="$page.props.flash && $page.props.flash.message" :active="true"
        @close="$page.props.flash.message = null">
        <div v-html="$page.props.flash.message"></div>
    </modal>
</template>
