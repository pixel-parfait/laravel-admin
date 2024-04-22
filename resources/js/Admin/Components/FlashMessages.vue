<template>
  <div>
    <section v-if="$page.props.flash.success && show" class="section py-5">
      <div class="notification is-success">
        <button class="delete" @click="show = false"></button>

        <div class="is-flex is-align-items-center">
          <span class="icon is-flex-shrink-0 mr-2">
            <CheckCircleIcon />
          </span>

          <p>{{ $page.props.flash.success }}</p>
        </div>
      </div>
    </section>

    <section
      v-if="
        ($page.props.flash.error ||
          Object.keys($page.props.errors).length > 0) &&
        show
      "
      class="section py-5"
    >
      <div class="notification is-danger" @click="showDetails = !showDetails">
        <button class="delete" @click="show = false"></button>

        <div class="is-flex is-align-items-center">
          <span class="icon mr-2">
            <ExclamationCircleIcon />
          </span>

          <p v-if="$page.props.flash.error">{{ $page.props.flash.error }}</p>
          <p v-else>
            <span v-if="Object.keys($page.props.errors).length === 1"
              >Le formulaire contient une erreur.</span
            >
            <span v-else
              >Le formulaire contient
              {{ Object.keys($page.props.errors).length }} erreurs.</span
            >
          </p>
        </div>

        <ul :class="{ 'is-hidden': !showDetails }">
          <li
            v-for="(error, index) in $page.props.errors"
            :key="`error-${index}`"
          >
            {{ index }} - {{ error }}
          </li>
        </ul>
      </div>
    </section>
  </div>
</template>

<script>
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
} from '@heroicons/vue/24/outline'

export default {
  data() {
    return {
      show: true,
      showDetails: false,
    }
  },
  watch: {
    '$page.props.flash': {
      handler() {
        this.show = true
      },
      deep: true,
    },
  },
  components: {
    CheckCircleIcon,
    ExclamationCircleIcon,
  },
}
</script>
