<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
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
    orders: {
        data: Array<{
            id: number;
            total_price: number;
            status: string;
            created_at: string;
            user: { name: string };
        }>;
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
    };
}>();

const onPageChange = (page: number) => {
    router.visit(toUrl(admin.orders.index({ query: { page } })), {
        preserveState: true,
        preserveScroll: true,
    });
};

const statusVariant = (status: string) => {
    switch (status) {
        case 'delivered': return 'default';
        case 'shipped': return 'default';
        case 'processing': return 'secondary';
        case 'cancelled': return 'destructive';
        default: return 'outline';
    }
};
</script>

<template>
    <Head title="Orders" />
    <AdminLayout>
        <div class="flex flex-col gap-4 p-4 md:p-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Orders</h1>
            </div>

            <div class="rounded-md border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">ID</TableHead>
                            <TableHead>Customer</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Total</TableHead>
                            <TableHead>Date</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="order in orders.data" :key="order.id">
                            <TableCell class="font-medium">#{{ order.id }}</TableCell>
                            <TableCell>{{ order.user?.name ?? 'Guest' }}</TableCell>
                            <TableCell>
                                <Badge :variant="statusVariant(order.status) as any">
                                    {{ order.status }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">${{ Number(order.total_price).toFixed(2) }}</TableCell>
                            <TableCell>{{ new Date(order.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell class="text-right">
                                <Button variant="outline" size="icon" as-child>
                                    <Link :href="toUrl(admin.orders.show(order.id))">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="orders.data.length === 0">
                            <TableCell colspan="6" class="h-24 text-center">
                                No orders found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="flex justify-end mt-4">
                <Pagination
                    :total="orders.total"
                    :sibling-count="1"
                    :items-per-page="orders.per_page"
                    :default-page="orders.current_page"
                    @update:page="onPageChange"
                >
                    <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst />
                        <PaginationPrevious />

                        <template v-for="(item, index) in items">
                            <PaginationItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                <Button class="w-10 h-10 p-0" :variant="item.value === orders.current_page ? 'default' : 'outline'">
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
