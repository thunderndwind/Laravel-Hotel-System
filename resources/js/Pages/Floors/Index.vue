<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'
// import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    floors: Object,
    isAdmin: Boolean,
    filters: Object,
})

const search = ref(props.filters.filter?.name || '')

const sort = ref(props.filters.sort || 'name')

watch(search, debounce((value) => {
    router.get(route('floors.index'), {
        filter: { name: value },
        sort: sort.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}, 300))

const handleSort = (column) => {
    const currentSort = sort.value
    const newSort = currentSort === column ? `-${column}` : column
    sort.value = newSort

    router.get(route('floors.index'), {
        filter: { name: search.value },
        sort: newSort,
    }, {
        preserveState: true,
        preserveScroll: true,
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
    <div>
        <div class="mb-4">
            <input v-model="search" type="text" placeholder="Search floors..." class="form-input" />
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th @click="handleSort('name')" class="cursor-pointer">
                        Name
                        <span v-if="sort === 'name'">↑</span>
                        <span v-if="sort === '-name'">↓</span>
                    </th>
                    <th @click="handleSort('number')" class="cursor-pointer">
                        Number
                        <span v-if="sort === 'number'">↑</span>
                        <span v-if="sort === '-number'">↓</span>
                    </th>
                    <th v-if="isAdmin">Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="floor in floors.data" :key="floor.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ floor.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ floor.number }}
                    </td>
                    <td v-if="isAdmin" class="px-6 py-4 whitespace-nowrap">
                        {{ floor.manager }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" v-if="floor.show_actions">
                        <div class="flex space-x-2">
                            <Link v-if="floor.can_edit" :href="route('floors.edit', floor.id)"
                                class="text-indigo-600 hover:text-indigo-900">
                            Edit
                            </Link>

                            <button v-if="floor.can_delete" @click="destroy(floor)"
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>

                            <button v-if="floor.deleted_at && floor.can_restore" @click="restore(floor)"
                                class="text-green-600 hover:text-green-900">
                                Restore
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- <Pagination :links="floors.links" class="mt-6" /> -->
    </div>
</template>