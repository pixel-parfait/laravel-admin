<script setup>
defineProps({
    title: {
        type: String,
        default: null,
    },
    description: {
        type: String,
        default: null,
        // @TODO: default meta description
    },
    ogType: {
        type: String,
        default: 'website',
    },
    ogImage: {
        type: String,
        default: null,
        // @TODO: default Opengraph image
    },
})
</script>

<template>

    <Head>
        <title>{{ title }}</title>

        <meta name="description" head-key="description" :content="description" />

        <meta property="og:title" head-key="og:title" :content="title" />
        <meta property="og:type" head-key="og:type" :content="ogType" />
        <meta property="og:image" head-key="og:image" :content="ogImage" />
    </Head>
</template>
