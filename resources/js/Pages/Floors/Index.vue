<script setup>
import { ref, computed, h } from "vue";
import { router, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { DataTable } from "@/Components/ui/data-table";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { toast } from "@/Components/ui/use-toast";
import DataTableHeader from "@/Components/ui/data-table/DataTableHeader.vue";
import { debounce } from "lodash";

import {
    MagnifyingGlassIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    floors: Object,
    isAdmin: Boolean,
    filters: Object,
});

const search = ref(props.filters?.search || "");
const isLoading = ref(false);
const sortBy = ref(props.filters?.sort || "-created_at");
const filterOptions = ref(props.filters?.filter || {});

// Add ref for last search value
const lastSearchValue = ref("");

// Optimized search handler with value comparison
const handleSearch = debounce((value) => {
    // Don't search if value hasn't changed or loading
    if (isLoading.value || value === lastSearchValue.value) return;

    isLoading.value = true;
    lastSearchValue.value = value; // Update last search value

    router.get(
        route("floors.index"),
        {
            ...(value ? { search: value } : {}),
            page: 1,
            per_page: pagination.value.per_page,
            sort: sortBy.value,
            filter: filterOptions.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
            onError: () => {
                // Reset last search value on error
                lastSearchValue.value = search.value;
            },
        },
    );
}, 300);

// Modified clear search handler
const clearSearch = () => {
    if (!search.value && lastSearchValue.value === "") return; // Don't clear if already empty

    search.value = "";
    lastSearchValue.value = "";
    handleSearch("");
    toast({
        title: "Search Cleared",
        description: "Showing all floors",
    });
};

// Sort handler
const handleSort = (column) => {
    const direction = sortBy.value === column ? "-" + column : column;
    sortBy.value = direction;

    isLoading.value = true;
    router.get(
        route("floors.index"),
        {
            sort: direction,
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
            search: search.value || undefined,
            filter: filterOptions.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};

const columns = computed(() => [
    {
        id: "name",
        header: "Name",
        accessorKey: "name",
        cell: ({ row }) => row.original.name,
        sortable: true,
        sortKey: "name",
    },
    {
        id: "number",
        header: "Number",
        accessorKey: "number",
        cell: ({ row }) => row.original.number,
        sortable: true,
        sortKey: "number",
    },
    ...(props.isAdmin
        ? [
              {
                  id: "manager",
                  header: "Manager",
                  accessorKey: "manager",
                  cell: ({ row }) => row.original.manager,
                  sortable: true,
                  sortKey: "manager_name",
              },
          ]
        : []),
    {
        id: "actions",
        header: "Actions",
        cell: ({ row }) => {
            if (!row.original.show_actions) return null;

            return h(
                "div",
                { class: "flex items-center gap-2" },
                [
                    row.original.can_edit &&
                        h(
                            Button,
                            {
                                variant: "outline",
                                size: "icon",
                                class: "h-8 w-8 text-blue-600 hover:text-blue-700",
                                onClick: () =>
                                    router.visit(
                                        route("floors.edit", row.original.id),
                                    ),
                            },
                            () => h(PencilIcon, { class: "h-4 w-4" }),
                        ),

                    row.original.can_delete &&
                        h(
                            Button,
                            {
                                variant: "outline",
                                size: "icon",
                                class: "h-8 w-8 text-red-600 hover:text-red-700",
                                onClick: () => destroy(row.original),
                            },
                            () => h(TrashIcon, { class: "h-4 w-4" }),
                        ),

                    row.original.deleted_at &&
                        row.original.can_restore &&
                        h(
                            Button,
                            {
                                variant: "outline",
                                size: "icon",
                                class: "h-8 w-8 text-green-600 hover:text-green-700",
                                onClick: () => restore(row.original),
                            },
                            () => h(ArrowPathIcon, { class: "h-4 w-4" }),
                        ),
                ].filter(Boolean),
            );
        },
    },
]);

const pagination = ref({
    current_page: props.floors.current_page,
    per_page: props.floors.per_page,
    total: props.floors.total,
    from: props.floors.from,
    to: props.floors.to,
});

const handlePaginationChange = (newPagination) => {
    isLoading.value = true;
    router.get(
        route("floors.index"),
        {
            page: newPagination.current_page,
            per_page: newPagination.per_page,
            search: search.value,
            ...props.filters,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};

const destroy = (floor) => {
    if (confirm(`Are you sure you want to delete floor ${floor.name}?`)) {
        router.delete(route("floors.destroy", floor.id));
    }
};

const restore = (floor) => {
    router.post(route("floors.restore", floor.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Floors Management
                </h2>
                <Button
                    v-if="$page.props.can.create_floors"
                    @click="$inertia.visit(route('floors.create'))"
                    class="flex items-center gap-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    Add New Floor
                </Button>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardContent class="p-6 pt-0">
                        <DataTable
                            :data="floors.data"
                            :columns="columns"
                            :pagination="pagination"
                            :is-loading="isLoading"
                            :search="search"
                            :sort-by="sortBy"
                            @sort="handleSort"
                            @update:pagination="handlePaginationChange"
                            @update:search="handleSearch"
                        />
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
