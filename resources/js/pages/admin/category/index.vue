<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Edit, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
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

const itemToDeleteId = ref<number | null>(null);
const isDeleteDialogOpen = ref(false);

const deleteCategory = (id: number) => {
    itemToDeleteId.value = id;
    isDeleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (itemToDeleteId.value) {
        router.delete(toUrl(admin.categories.destroy(itemToDeleteId.value)), {
            onFinish: () => {
                isDeleteDialogOpen.value = false;
                itemToDeleteId.value = null;
            },
        });
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

            <div
                class="overflow-hidden rounded-xl border border-border/50 bg-background shadow-sm"
            >
                <Table>
                    <TableHeader>
                        <TableRow
                            class="border-b-border/50 hover:bg-transparent"
                        >
                            <TableHead
                                class="w-[80px] text-xs tracking-wider text-muted-foreground uppercase"
                                >ID</TableHead
                            >
                            <TableHead
                                class="text-xs tracking-wider text-muted-foreground uppercase"
                                >Name</TableHead
                            >
                            <TableHead
                                class="text-xs tracking-wider text-muted-foreground uppercase"
                                >Slug</TableHead
                            >
                            <TableHead
                                class="text-right text-xs tracking-wider text-muted-foreground uppercase"
                                >Actions</TableHead
                            >
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="category in categories.data"
                            :key="category.id"
                            class="border-b-border/50 transition-colors hover:bg-muted/40"
                        >
                            <TableCell
                                class="text-xs font-medium text-muted-foreground"
                            >
                                #{{ category.id }}
                            </TableCell>
                            <TableCell class="font-medium text-foreground">
                                {{ category.name }}
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                {{ category.slug }}
                            </TableCell>
                            <TableCell class="text-right">
                                <div
                                    class="flex justify-end gap-1 opacity-80 transition-opacity hover:opacity-100"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                toUrl(
                                                    admin.categories.edit(
                                                        category.id,
                                                    ),
                                                )
                                            "
                                        >
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                        @click="deleteCategory(category.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="categories.data.length === 0">
                            <TableCell
                                colspan="4"
                                class="h-32 text-center text-muted-foreground"
                            >
                                No categories found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="mt-4 flex justify-end">
                <Pagination
                    :total="categories.total"
                    :sibling-count="1"
                    :items-per-page="categories.per_page"
                    :default-page="categories.current_page"
                    @update:page="onPageChange"
                >
                    <PaginationContent
                        v-slot="{ items }"
                        class="flex items-center gap-1"
                    >
                        <PaginationFirst />
                        <PaginationPrevious />

                        <template v-for="(item, index) in items">
                            <PaginationItem
                                v-if="item.type === 'page'"
                                :key="index"
                                :value="item.value"
                                as-child
                            >
                                <Button
                                    class="h-10 w-10 p-0"
                                    :variant="
                                        item.value === categories.current_page
                                            ? 'default'
                                            : 'outline'
                                    "
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationItem>
                            <PaginationEllipsis
                                v-else
                                :key="item.type"
                                :index="index"
                            />
                        </template>

                        <PaginationNext />
                        <PaginationLast />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>

        <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                    <AlertDialogDescription>
                        This action cannot be undone. This will permanently delete the category
                        and remove all associated data from our servers.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction class="bg-destructive hover:bg-destructive/90" @click="confirmDelete">
                        Delete Category
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AdminLayout>
</template>
