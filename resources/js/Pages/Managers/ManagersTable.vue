<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef } from '@tanstack/vue-table'


import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { defineProps, watchEffect } from 'vue';
import Button from '@/Components/ui/button/Button.vue';


const props = defineProps<{
  columns: ColumnDef<any, any>[]
  data: any[]
}>()
// console.log('data:', props.data);


const table = useVueTable({
  get data() { return props.data },
  get columns() { return props.columns },
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),

  state: {
    pagination: {
      pageIndex: 0,
      pageSize: 5, // or whatever number of rows per page you want
    }
  },
  onPaginationChange: (updater) => {
    const newPagination = typeof updater === 'function' ? updater(table.getState().pagination) : updater
    table.setOptions((prev) => ({
      ...prev,
      state: {
        ...prev.state,
        pagination: newPagination,
      },
    }));
  },
  manualPagination: false,
  debugTable: true,

})

watchEffect(() => {
  console.log('Pagination State:', table.getState().pagination)
})
console.log('nexxxxxtttttttttt:',table.getCanNextPage());

</script>

<template>
  <div>
    <div class="border rounded-md">
    <Table>
      <TableHeader>
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
          <TableHead v-for="header in headerGroup.headers" :key="header.id">
            <FlexRender
              v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template v-if="table.getRowModel() && table.getRowModel().rows && table.getRowModel().rows.length">
          <TableRow
            v-for="row in table.getPaginationRowModel().rows" :key="row.id"
            :data-state="row.getIsSelected() ? 'selected' : undefined"
          >
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </TableCell>
          </TableRow>
        </template>
        <template v-else>
          <TableRow>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              No results.
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
    </div>
    <div class="flex items-center justify-end py-4 space-x-2">
      <Button
        variant="outline"
        size="sm"
        :disabled="!table.getCanPreviousPage()"
        @click="table.previousPage()"
      >
        Previous
      </Button>
      <Button
        variant="outline"
        size="sm"
        :disabled="!table.getCanNextPage()"
        @click="table.nextPage()"
      >
        Next
      </Button>
    </div>

  </div>
  
</template>