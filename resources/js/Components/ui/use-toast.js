import { ref } from 'vue'

const toasts = ref([])

export function toast({ title, description, variant = 'default', duration = 3000 }) {
    const id = Date.now()
    
    toasts.value.push({
        id,
        title,
        description,
        variant,
    })

    setTimeout(() => {
        toasts.value = toasts.value.filter(toast => toast.id !== id)
    }, duration)
}

export default {

        toasts,
        toast,

}
