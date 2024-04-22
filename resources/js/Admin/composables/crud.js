import { onMounted, onUnmounted, inject } from 'vue'
import { router } from '@inertiajs/vue3'

export function useCrud(form, args) {
  let removeRouteChangeEventListener

  const swalPrimary = inject('swalPrimary')
  const swalDanger = inject('swalDanger')

  onMounted(() => {
    removeRouteChangeEventListener = router.on('before', (event) => {
      if (form.isDirty) {
        return confirm('Attention : le formulaire contient des informations non enregistrées. Voulez-vous continuer ?')
      }

      return true
    })
  })
  onUnmounted(() => {
    removeRouteChangeEventListener()
  })

  const store = () => {
    removeRouteChangeEventListener()

    form.post(route(`${args.route.prefix}.store`, args.route.params), args.options)
  }

  const update = () => {
    removeRouteChangeEventListener()

    form.put(route(`${args.route.prefix}.update`, args.route.params), args.options)
  }

  const destroy = () => {
    swalDanger.fire({
      title: 'Suppression',
      html: args.messages.destroy,
      confirmButtonText: 'Supprimer',
      focusCancel: true,
    }).then((result) => {
      if (result.isConfirmed) {
        form.delete(route(`${args.route.prefix}.destroy`, args.route.params), args.options)
      }
    })
  }

  const restore = () => {
    swalPrimary.fire({
      title: 'Restauration',
      html: args.messages.restore,
      confirmButtonText: 'Confirmer',
      focusCancel: true,
    }).then((result) => {
      if (result.isConfirmed) {
        form.put(route(`${args.route.prefix}.restore`, args.route.params), args.options)
      }
    })
  }

  const forceDelete = () => {
    swalDanger.fire({
      title: 'Suppression définitive',
      html: args.messages.forceDelete,
      confirmButtonText: 'Supprimer',
      focusCancel: true,
    }).then((result) => {
      if (result.isConfirmed) {
        form.delete(route(`${args.route.prefix}.destroy`, args.route.params), args.options)
      }
    })
  }

  return { store, update, destroy, restore, forceDelete }
}
