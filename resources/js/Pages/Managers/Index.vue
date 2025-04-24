<script setup>
import { ref, computed, h } from "vue";
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
    EyeIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    managers: Object,
    filters: Object,
});

const search = ref(props.filters?.search || "");
const isLoading = ref(false);
const sortBy = ref(props.filters?.sort || "-created_at");
const lastSearchValue = ref("");

const handleSearch = debounce((value) => {
    if (isLoading.value || value === lastSearchValue.value) return;

    isLoading.value = true;
    lastSearchValue.value = value;

    router.get(
        route("managers.index"),
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

const handleSort = (sortKey) => {
    if (isLoading.value) return;

    isLoading.value = true;
    const direction = sortBy.value === sortKey ? `-${sortKey}` : sortKey;
    sortBy.value = direction;

    router.get(
        route("managers.index"),
        {
            search: search.value,
            sort: direction,
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
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
        id: "avatar",
        header: "Avatar",
        cell: ({ row }) =>
            h("img", {
                src: row.original.avatar_image,
                class: "w-12 h-12 rounded-full",
                alt: "Avatar",
            }),
    },
    {
        id: "name",
        header: "Name",
        accessorKey: "name",
        cell: ({ row }) => row.original.name,
        sortable: true,
        sortKey: "name",
    },
    {
        id: "email",
        header: "Email",
        accessorKey: "email",
        cell: ({ row }) => row.original.email,
        sortable: true,
        sortKey: "email",
    },
    {
        id: "national_id",
        header: "National ID",
        accessorKey: "national_id",
        cell: ({ row }) => row.original.national_id,
    },
    {
        id: "phone_number",
        header: "Phone",
        accessorKey: "phone_number",
        cell: ({ row }) => row.original.phone_number,
    },
    {
        id: "actions",
        header: "Actions",
        cell: ({ row }) => {
            if (!row.original.show_actions) return null;

            return h("div", { class: "flex items-center gap-2" }, [
                h(
                    Button,
                    {
                        variant: "outline",
                        size: "icon",
                        class: "h-8 w-8 text-green-600 hover:text-green-700",
                        onClick: () =>
                            router.visit(
                                route("managers.show", row.original.id),
                            ),
                    },
                    () => h(EyeIcon, { class: "h-4 w-4" }),
                ),
                row.original.can_edit &&
                    h(
                        Button,
                        {
                            variant: "outline",
                            size: "icon",
                            class: "h-8 w-8 text-blue-600 hover:text-blue-700",
                            onClick: () =>
                                router.visit(
                                    route("managers.edit", row.original.id),
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
            ]);
        },
    },
]);

const pagination = ref({
    current_page: props.managers.current_page,
    per_page: props.managers.per_page,
    total: props.managers.total,
    from: props.managers.from,
    to: props.managers.to,
});

const handlePaginationChange = (newPagination) => {
    isLoading.value = true;
    router.get(
        route("managers.index"),
        {
            page: newPagination.current_page,
            per_page: newPagination.per_page,
            search: search.value,
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
};

const destroy = (manager) => {
    if (confirm(`Are you sure you want to delete manager ${manager.name}?`)) {
        router.delete(route("managers.destroy", manager.id));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Managers Management
                </h2>
                <Button
                    v-if="$page.props.can.create_managers"
                    @click="$inertia.visit(route('managers.create'))"
                    class="flex items-center gap-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    Add New Manager
                </Button>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardContent class="p-6 pt-0">
                        <DataTable
                            :data="managers.data"
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
