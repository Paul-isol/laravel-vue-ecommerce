<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    CardFooter,
} from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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

const props = defineProps<{
    order: {
        id: number;
        total_price: number;
        status: string;
        session_id: string;
        created_at: string;
        user?: { id: number; name: string; email: string } | null;
        address?: {
            address1: string;
            address2?: string;
            city: string;
            state: string;
            zipcode: string;
            country_code: string;
        } | null;
        items: Array<{
            id: number;
            quantity: number;
            unit_price: number;
            product?: { id: number; title: string } | null;
        }>;
    }
}>();

const statusForm = useForm({
    status: props.order.status,
    _method: 'put',
});

const updateStatus = () => {
    statusForm.post(toUrl(admin.orders.update(props.order.id)));
};

const statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
</script>

<template>
    <Head :title="`Order #${order.id}`" />
    <AdminLayout>
        <div class="flex flex-col gap-4 p-4 md:p-8">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="toUrl(admin.orders.index())">
                        <ChevronLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <h1 class="text-2xl font-bold tracking-tight">Order #{{ order.id }}</h1>
                <Badge>{{ order.status }}</Badge>
            </div>

            <div class="grid gap-4 md:grid-cols-[1fr_350px] lg:gap-8 mt-4">
                <div class="grid auto-rows-max items-start gap-4 lg:gap-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>Order Items</CardTitle>
                            <CardDescription>Products included in this order.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Product</TableHead>
                                        <TableHead class="text-right">Qty</TableHead>
                                        <TableHead class="text-right">Unit Price</TableHead>
                                        <TableHead class="text-right">Total</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in order.items" :key="item.id">
                                        <TableCell>{{ item.product?.title ?? 'Deleted Product' }}</TableCell>
                                        <TableCell class="text-right">{{ item.quantity }}</TableCell>
                                        <TableCell class="text-right">${{ Number(item.unit_price).toFixed(2) }}</TableCell>
                                        <TableCell class="text-right">${{ (item.quantity * Number(item.unit_price)).toFixed(2) }}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </CardContent>
                        <CardFooter class="flex justify-end border-t pt-4">
                            <div class="text-lg font-semibold">
                                Total: ${{ Number(order.total_price).toFixed(2) }}
                            </div>
                        </CardFooter>
                    </Card>
                </div>

                <div class="grid auto-rows-max items-start gap-4 lg:gap-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>Customer</CardTitle>
                        </CardHeader>
                        <CardContent class="grid gap-2 text-sm">
                            <div><span class="font-medium">Name:</span> {{ order.user?.name ?? 'Guest' }}</div>
                            <div><span class="font-medium">Email:</span> {{ order.user?.email ?? 'N/A' }}</div>
                            <div><span class="font-medium">Date:</span> {{ new Date(order.created_at).toLocaleString() }}</div>
                        </CardContent>
                    </Card>

                    <Card v-if="order.address">
                        <CardHeader>
                            <CardTitle>Shipping Address</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm">
                            <p>{{ order.address.address1 }}</p>
                            <p v-if="order.address.address2">{{ order.address.address2 }}</p>
                            <p>{{ order.address.city }}, {{ order.address.state }} {{ order.address.zipcode }}</p>
                            <p>{{ order.address.country_code }}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Update Status</CardTitle>
                        </CardHeader>
                        <CardContent class="grid gap-4">
                            <Select v-model="statusForm.status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="s in statuses" :key="s" :value="s">
                                        {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </CardContent>
                        <CardFooter class="border-t px-6 py-4">
                            <Button @click="updateStatus" :disabled="statusForm.processing" class="w-full">
                                Update Status
                            </Button>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
