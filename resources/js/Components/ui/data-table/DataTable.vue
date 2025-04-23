<script setup>
import { FlexRender, getCoreRowModel, useVueTable } from "@tanstack/vue-table";
import PageSizeSelect from "./PageSizeSelect.vue";
import SearchInput from "./SearchInput.vue";

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
    pagination: {
        type: Object,
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    search: {
        type: String,
        default: "",
    },
    sortBy: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:pagination", "update:search", "sort"]);

const table = useVueTable({
    get data() {
        return props.data;
    },
    columns: props.columns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    pageCount: Math.ceil(props.pagination.total / props.pagination.per_page),
    state: {
        pagination: {
            pageIndex: props.pagination.current_page - 1,
            pageSize: props.pagination.per_page,
        },
    },
    onPaginationChange: (updater) => {
        const newPagination = updater(table.getState().pagination);
        emit("update:pagination", {
            current_page: newPagination.pageIndex + 1,
            per_page: newPagination.pageSize,
        });
    },
});

const handleSort = (sortKey) => {
    emit("sort", sortKey);
};

const handleSearch = (value) => {
    emit("update:search", value);
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 pt-4">
            <SearchInput
                :model-value="search"
                placeholder="Search by name, number or manager..."
                class="w-[350px]"
                @update:model-value="$emit('update:search', $event)"
                @search="handleSearch"
                @clear="handleSearch('')"
            />
            <div class="flex items-center gap-2">
                <label for="pageSize" class="text-sm text-gray-600">Show</label>
                <PageSizeSelect
                    id="pageSize"
                    :model-value="pagination.per_page"
                    @update:model-value="
                        $emit('update:pagination', {
                            ...pagination,
                            per_page: $event,
                        })
                    "
                />
                <span class="text-sm text-gray-600">entries</span>
            </div>
        </div>

        <div class="rounded-md border">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-slate-50">
                        <th
                            v-for="header in table.getHeaderGroups()[0].headers"
                            :key="header.id"
                            class="h-12 px-4 text-left align-middle font-medium text-slate-500"
                        >
                            <div
                                class="flex items-center space-x-2"
                                :class="{
                                    'cursor-pointer':
                                        header.column.columnDef.sortable,
                                }"
                                @click="
                                    header.column.columnDef.sortable &&
                                    handleSort(header.column.columnDef.sortKey)
                                "
                            >
                                <span>{{
                                    header.column.columnDef.header
                                }}</span>
                                <span
                                    v-if="header.column.columnDef.sortable"
                                    class="ml-1"
                                >
                                    {{
                                        sortBy ===
                                        header.column.columnDef.sortKey
                                            ? "↑"
                                            : sortBy ===
                                                `-${header.column.columnDef.sortKey}`
                                              ? "↓"
                                              : ""
                                    }}
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                        class="border-b transition-colors hover:bg-slate-100"
                    >
                        <td
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                            class="p-4 align-middle"
                        >
                            <component
                                :is="cell.column.columnDef.cell"
                                :row="cell.row"
                                :value="cell.getValue()"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between px-4 py-4">
            <div class="flex-1 text-sm text-muted-foreground">
                {{ pagination.from }}-{{ pagination.to }} of
                {{ pagination.total }} items
            </div>
            <div class="flex items-center space-x-6 lg:space-x-8">
                <div
                    class="flex w-[100px] items-center justify-center text-sm font-medium"
                >
                    Page {{ pagination.current_page }} of
                    {{ Math.ceil(pagination.total / pagination.per_page) }}
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
                        :disabled="pagination.current_page === 1"
                        @click="
                            $emit('update:pagination', {
                                ...pagination,
                                current_page: pagination.current_page - 1,
                            })
                        "
                    >
                        Previous
                    </button>
                    <button
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
                        :disabled="
                            pagination.current_page >=
                            Math.ceil(pagination.total / pagination.per_page)
                        "
                        @click="
                            $emit('update:pagination', {
                                ...pagination,
                                current_page: pagination.current_page + 1,
                            })
                        "
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
