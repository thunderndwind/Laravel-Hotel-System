import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function useRole() {
  const page = usePage()
  
  const hasRole = (role) => {
    return page.props.auth.user?.roles?.some(r => r.name === role.toLowerCase())
  }

  const isAdmin = computed(() => hasRole('admin'))
  const isManager = computed(() => hasRole('manager'))
  const isReceptionist = computed(() => hasRole('receptionist'))
  const isClient = computed(() => hasRole('client'))

  return {
    hasRole,
    isAdmin,
    isManager,
    isReceptionist,
    isClient
  }
}