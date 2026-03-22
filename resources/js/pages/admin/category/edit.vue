<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    CardFooter,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toUrl } from '@/lib/utils';
import admin from '@/routes/admin';
import AdminLayout from '../AdminLayout.vue';

const props = defineProps<{
    category: {
        id: number;
        name: string;
        slug: string;
    }
}>();

const form = useForm({
    name: props.category.name,
    _method: 'put',
});

const submit = () => {
    form.post(toUrl(admin.categories.update(props.category.id)));
};
</script>

<template>
    <Head title="Edit Category" />
    <AdminLayout>
        <div class="flex flex-col gap-4 p-4 md:p-8">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="toUrl(admin.categories.index())">
                        <ChevronLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <h1 class="text-2xl font-bold tracking-tight">Edit Category</h1>
            </div>

            <div class="grid gap-4 md:grid-cols-[1fr_250px] lg:grid-cols-3 lg:gap-8 mt-4">
                <div class="grid auto-rows-max items-start gap-4 lg:col-span-2 lg:gap-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>Category Details</CardTitle>
                            <CardDescription>
                                Edit the information for this category.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submit" class="grid gap-6">
                                <div class="grid gap-3">
                                    <Label for="name">Name</Label>
                                    <Input
                                        id="name"
                                        type="text"
                                        class="w-full"
                                        v-model="form.name"
                                        placeholder="Enter category name"
                                    />
                                    <span v-if="form.errors.name" class="text-sm text-destructive">
                                        {{ form.errors.name }}
                                    </span>
                                </div>
                            </form>
                        </CardContent>
                        <CardFooter class="justify-between space-x-2">
                            <Button variant="ghost" as-child>
                                <Link :href="toUrl(admin.categories.index())">Cancel</Link>
                            </Button>
                            <Button @click="submit" :disabled="form.processing">Save Changes</Button>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
