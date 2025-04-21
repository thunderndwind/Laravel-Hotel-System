<script setup>
import { ref, computed, h, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import DataTable from '@/Components/ui/data-table/DataTable.vue'

const props = defineProps({
    floors: Object,
    isAdmin: Boolean,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const isLoading = ref(false)
const previousSearch = ref(props.filters?.search || '')

// Enhanced search handler
const handleSearch = (value) => {
    isLoading.value = true
    router.get(route('floors.index'), {
        ...(value ? { search: value } : {}),
        page: 1,
        per_page: pagination.value.per_page,
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false
        }
    })
}

// Watch search with validation
watch(search, (newValue) => {
    // Trim the search value
    const trimmedValue = newValue?.trim() || ''

    // Only trigger search if minimum length or empty
    if (trimmedValue === '' || trimmedValue.length >= 2) {
        handleSearch(trimmedValue)
    }
})

const columns = computed(() => [
    {
        id: 'name',
        header: 'Name',
        accessorKey: 'name',
        cell: ({ row }) => h('span', {}, row.original.name),
    },
    {
        id: 'number',
        header: 'Number',
        accessorKey: 'number',
        cell: ({ row }) => h('span', {}, row.original.number),
    },
    ...(props.isAdmin ? [{
        id: 'manager',
        header: 'Manager',
        accessorKey: 'manager',
        cell: ({ row }) => h('span', {}, row.original.manager),
    }] : []),
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            if (!row.original.show_actions) return null

            return h('div', { class: 'flex space-x-2' }, [
                row.original.can_edit && h(Link, {
                    href: route('floors.edit', row.original.id),
                    class: 'text-indigo-600 hover:text-indigo-900'
                }, { default: () => 'Edit' }),

                row.original.can_delete && h('button', {
                    onClick: () => destroy(row.original),
                    class: 'text-red-600 hover:text-red-900'
                }, 'Delete'),

                row.original.deleted_at && row.original.can_restore && h('button', {
                    onClick: () => restore(row.original),
                    class: 'text-green-600 hover:text-green-900'
                }, 'Restore')
            ].filter(Boolean))
        }
    }
])

const pagination = ref({
    current_page: props.floors.current_page,
    per_page: props.floors.per_page,
    total: props.floors.total,
    from: props.floors.from,
    to: props.floors.to,
})

const handlePaginationChange = (newPagination) => {
    isLoading.value = true
    router.get(route('floors.index'), {
        page: newPagination.current_page,
        per_page: newPagination.per_page,
        search: search.value,
        ...props.filters,
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false
        }
    })
}

const destroy = (floor) => {
    if (confirm(`Are you sure you want to delete floor ${floor.name}?`)) {
        router.delete(route('floors.destroy', floor.id))
    }
}

const restore = (floor) => {
    router.post(route('floors.restore', floor.id))
}
</script>

<template>
    <div class="p-4">
        <DataTable :data="floors.data" :columns="columns" :pagination="pagination" :is-loading="isLoading"
            :search="search" @update:pagination="handlePaginationChange" @update:search="handleSearch" />
    </div>
</template>