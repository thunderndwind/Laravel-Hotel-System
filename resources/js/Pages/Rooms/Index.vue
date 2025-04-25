<script setup>
import { ref, computed, h, watch } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { DataTable } from "@/Components/ui/data-table";
import { Button } from "@/Components/ui/button";
import { Card, CardContent } from "@/Components/ui/card";
import { toast } from "@/Components/ui/use-toast";
import { debounce } from "lodash";
import {
    MagnifyingGlassIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ArrowPathIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    rooms: Object,
    isAdmin: Boolean,
    filters: Object,
});

const search = ref(props.filters?.search || "");
const isLoading = ref(false);
const sortBy = ref(props.filters?.sort || "-created_at");
const lastSearchValue = ref("");

const pagination = ref({
    current_page: props.rooms.current_page,
    per_page: props.rooms.per_page,
    total: props.rooms.total,
    from: props.rooms.from,
    to: props.rooms.to,
    last_page: props.rooms.last_page,
});

watch(
    () => props.rooms,
    (newRooms) => {
        pagination.value = {
            current_page: newRooms.current_page,
            per_page: newRooms.per_page,
            total: newRooms.total,
            from: newRooms.from,
            to: newRooms.to,
            last_page: newRooms.last_page,
        };
    },
    { immediate: true },
);

const handleSearch = debounce((value) => {
    if (isLoading.value || value === lastSearchValue.value) return;

    isLoading.value = true;
    lastSearchValue.value = value;

    router.get(
        route("rooms.index"),
        {
            ...(value ? { search: value } : {}),
            page: 1,
            per_page: pagination.value.per_page,
            sort: sortBy.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
}, 300);

const columns = computed(() => [
    {
        id: "number",
        header: "Number",
        accessorKey: "number",
        cell: ({ row }) => row.original.number,
        sortable: true,
        sortKey: "number",
    },
    {
        id: "price",
        header: "Price",
        accessorKey: "price",
        cell: ({ row }) => `$${row.original.price}`,
        sortable: true,
        sortKey: "price",
    },
    {
        id: "capacity",
        header: "Capacity",
        accessorKey: "capacity",
        cell: ({ row }) => row.original.capacity,
        sortable: true,
        sortKey: "capacity",
    },
    {
        id: "floor",
        header: "Floor",
        accessorKey: "floor",
        cell: ({ row }) => row.original.floor,
        sortable: true,
        sortKey: "floor_name",
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
                                        route("rooms.edit", row.original.id),
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

const handleSort = (column) => {
    const direction = sortBy.value === column ? `-${column}` : column;
    sortBy.value = direction;

    isLoading.value = true;
    router.get(
        route("rooms.index"),
        {
            sort: direction,
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
            ...(search.value ? { search: search.value } : {}),
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

const handlePaginationChange = (newPagination) => {
    if (isLoading.value) return;

    isLoading.value = true;
    router.get(
        route("rooms.index"),
        {
            page: newPagination.current_page,
            per_page: newPagination.per_page,
            sort: sortBy.value,
            ...(search.value ? { search: search.value } : {}),
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
                pagination.value = {
                    ...pagination.value,
                    current_page: props.rooms.current_page,
                    per_page: props.rooms.per_page,
                    total: props.rooms.total,
                    from: props.rooms.from,
                    to: props.rooms.to,
                    last_page: props.rooms.last_page,
                };
            },
        },
    );
};

const destroy = (room) => {
    if (confirm(`Are you sure you want to delete room ${room.number}?`)) {
        router.delete(route("rooms.destroy", room.id), {
            onSuccess: () => {
                toast({
                    title: "Success",
                    description: "Room deleted successfully",
                });
            },
        });
    }
};

const restore = (room) => {
    router.put(
        route("rooms.restore", room.id),
        {},
        {
            onSuccess: () => {
                toast({
                    title: "Success",
                    description: "Room restored successfully",
                });
            },
        },
    );
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Rooms Management
                </h2>
                <Button
                    v-if="$page.props.can.create_rooms"
                    @click="$inertia.visit(route('rooms.create'))"
                    class="flex items-center gap-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    Add New Room
                </Button>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardContent class="p-6">
                        <DataTable
                            :data="rooms.data"
                            :columns="columns"
                            :pagination="pagination"
                            :is-loading="isLoading"
                            :search="search"
                            :sort-by="sortBy"
                            :page-sizes="[10, 25, 50, 100]"
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
