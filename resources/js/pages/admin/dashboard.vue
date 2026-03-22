<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ShoppingBag, ShoppingCart, Users, Tags } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AdminLayout from './AdminLayout.vue';

defineProps<{
    metrics: {
        total_products: number;
        total_orders: number;
        total_users: number;
        total_brands: number;
    }
}>();
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="flex flex-col gap-4 p-4 md:p-8">
            <h1 class="text-2xl font-bold tracking-tight">Dashboard Overview</h1>
            
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mt-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Revenue</CardTitle>
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.total_orders }}</div>
                        <p class="text-xs text-muted-foreground">Orders Placed</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Products</CardTitle>
                        <ShoppingBag class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.total_products }}</div>
                        <p class="text-xs text-muted-foreground">Products in catalog</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Registered Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.total_users }}</div>
                        <p class="text-xs text-muted-foreground">Customers & Admins</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Store Brands</CardTitle>
                        <Tags class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.total_brands }}</div>
                        <p class="text-xs text-muted-foreground">Brands mapped</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>