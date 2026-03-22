<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Edit, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationContent,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { toUrl } from '@/lib/utils';
import admin from '@/routes/admin';
import AdminLayout from '../AdminLayout.vue';

defineProps<{
    categories: {
        data: Array<{
            id: number;
            name: string;
            slug: string;
            created_at: string;
        }>;
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
    };
}>();

const deleteCategory = (id: number) => {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(toUrl(admin.categories.destroy(id)));
    }
};

const onPageChange = (page: number) => {
    router.visit(toUrl(admin.categories.index({ query: { page } })), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Categories" />
    <AdminLayout>
        <div class="flex flex-col gap-4 p-4 md:p-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Categories</h1>
                <Button as-child>
                    <Link :href="toUrl(admin.categories.create())">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Category
                    </Link>
                </Button>
            </div>

            <div class="rounded-md border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Slug</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="category in categories.data" :key="category.id">
                            <TableCell class="font-medium">
                                {{ category.id }}
                            </TableCell>
                            <TableCell>{{ category.name }}</TableCell>
                            <TableCell>{{ category.slug }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="icon" as-child>
                                        <Link :href="toUrl(admin.categories.edit(category.id))">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="destructive" size="icon" @click="deleteCategory(category.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="categories.data.length === 0">
                            <TableCell colspan="4" class="h-24 text-center">
                                No categories found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="flex justify-end mt-4">
                <Pagination
                    :total="categories.total"
                    :sibling-count="1"
                    :items-per-page="categories.per_page"
                    :default-page="categories.current_page"
                    @update:page="onPageChange"
                >
                    <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst />
                        <PaginationPrevious />

                        <template v-for="(item, index) in items">
                            <PaginationItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                <Button class="w-10 h-10 p-0" :variant="item.value === categories.current_page ? 'default' : 'outline'">
                                    {{ item.value }}
                                </Button>
                            </PaginationItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext />
                        <PaginationLast />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </AdminLayout>
</template>
