<template>
  <button type="button" @click="show = true">
    <slot />

    <teleport v-if="show" to="#dropdown">
      <div>
        <div
          style="
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
            z-index: 99998;
            background: black;
            opacity: 0.2;
          "
          @click="show = false"
        />
        <div
          class="dropdown"
          ref="dropdown"
          style="position: absolute; z-index: 99999"
          @click.stop="show = !autoClose"
        >
          <div class="dropdown-inner">
            <slot name="dropdown" />
          </div>

          <div class="dropdown-arrow" ref="arrow" v-if="arrow"></div>
        </div>
      </div>
    </teleport>
  </button>
</template>

<script>
import { createPopper } from '@popperjs/core'

export default {
  props: {
    placement: {
      type: String,
      default: 'bottom-end',
    },
    autoClose: {
      type: Boolean,
      default: true,
    },
    arrow: {
      type: Boolean,
      default: true,
    },
    sameWidth: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      show: false,
    }
  },
  watch: {
    show(show) {
      if (show) {
        this.$nextTick(() => {
          this.popper = createPopper(this.$el, this.$refs.dropdown, {
            placement: this.placement,
            modifiers: [
              {
                name: 'preventOverflow',
                options: {
                  altBoundary: true,
                },
              },
              {
                name: 'offset',
                options: {
                  offset: [0, 10],
                },
              },
              {
                name: 'arrow',
                enabled: this.arrow,
                options: {
                  element: this.$refs.arrow,
                  padding: 10,
                },
              },
              {
                name: 'sameWidth',
                enabled: this.sameWidth,
                phase: 'beforeWrite',
                requires: ['computeStyles'],
                fn: ({ state }) => {
                  state.styles.popper.width = `${state.rects.reference.width}px`
                },
                effect: ({ state }) => {
                  state.elements.popper.style.width = `${state.elements.reference.offsetWidth}px`
                },
              },
            ],
          })
        })
      } else if (this.popper) {
        setTimeout(() => this.popper.destroy(), 100)
      }
    },
  },
  mounted() {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.show = false
      }
    })
  },
}
</script>
