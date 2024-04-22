<template>
    <Teleport to="#modals">
        <Transition name="modal">
            <div class="modal fixed inset-0 z-40 flex items-center justify-center overflow-hidden p-4" v-if="active">
                <div class="modal-background absolute inset-0 z-30 transition-all duration-500" @click="emit('close')">
                </div>

                <div class="modal-content relative z-30 w-full max-w-xl text-white transition-all duration-150">
                    <div class="overflow-auto p-8 text-center">
                        <slot />
                    </div>

                    <button
                        class="absolute right-2 top-2 z-30 flex h-6 w-6 cursor-pointer items-center hover:opacity-60"
                        aria-label="Fermer" @click="emit('close')">
                        Fermer
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    active: [Boolean, Number]
})

const emit = defineEmits(['close'])
</script>

<style lang="scss">
.modal {

    &-enter-active,
    &-leave-active {
        transition: opacity 500ms ease;

        .modal-background {
            opacity: 1;
        }

        .modal-content {
            opacity: 1;
        }
    }

    &-enter-from,
    &-leave-to {
        .modal-background {
            opacity: 0;
        }

        .modal-content {
            opacity: 0;
            transform: translateY(3rem);
        }
    }
}
</style>
