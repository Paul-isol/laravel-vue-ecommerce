<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft, Upload, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    CardFooter,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { toUrl } from '@/lib/utils';
import admin from '@/routes/admin';
import AdminLayout from '../AdminLayout.vue';

interface ProductImageData {
    id: number;
    image: string;
    product_id: number;
}

const props = defineProps<{
    product: {
        id: number;
        title: string;
        price: number;
        quantity: number;
        description: string | null;
        brand_id: number | null;
        category_id: number | null;
        published: boolean;
        images: ProductImageData[];
    };
    brands: Array<{ id: number; name: string }>;
    categories: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    title: props.product.title,
    price: props.product.price,
    quantity: props.product.quantity,
    description: props.product.description ?? '',
    brand_id: props.product.brand_id ? String(props.product.brand_id) : '' as string | number,
    category_id: props.product.category_id ? String(props.product.category_id) : '' as string | number,
    published: Boolean(props.product.published),
    new_images: [] as File[],
    deleted_images: [] as number[],
    _method: 'put',
});

const newPreviews = ref<string[]>([]);
const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const deletedExistingIds = ref<Set<number>>(new Set());

const handleFiles = (files: FileList | null) => {
    if (!files) {
        return;
    }

    for (const file of Array.from(files)) {
        if (!file.type.startsWith('image/')) {
            continue;
        }
        
        form.new_images.push(file);
        newPreviews.value.push(URL.createObjectURL(file));
    }
};

const onDrop = (e: DragEvent) => {
    isDragging.value = false;
    handleFiles(e.dataTransfer?.files ?? null);
};

const onFileSelect = (e: Event) => {
    const target = e.target as HTMLInputElement;
    handleFiles(target.files);
    target.value = '';
};

const removeNewImage = (index: number) => {
    URL.revokeObjectURL(newPreviews.value[index]);
    newPreviews.value.splice(index, 1);
    form.new_images.splice(index, 1);
};

const markExistingForDeletion = (imageId: number) => {
    deletedExistingIds.value.add(imageId);
    form.deleted_images.push(imageId);
};

const undoExistingDeletion = (imageId: number) => {
    deletedExistingIds.value.delete(imageId);
    form.deleted_images = form.deleted_images.filter(id => id !== imageId);
};

const submit = () => {
    form.post(toUrl(admin.products.update(props.product.id)), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Product" />
    <AdminLayout>
        <div class="flex flex-col gap-4 p-4 md:p-8">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="toUrl(admin.products.index())">
                        <ChevronLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <h1 class="text-2xl font-bold tracking-tight">Edit Product</h1>
            </div>

            <div class="grid gap-4 md:grid-cols-[1fr_250px] lg:grid-cols-3 lg:gap-8 mt-4">
                <div class="grid auto-rows-max items-start gap-4 lg:col-span-2 lg:gap-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>Product Details</CardTitle>
                            <CardDescription>Update product information.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submit" class="grid gap-6">
                                <div class="grid gap-3">
                                    <Label for="title">Title</Label>
                                    <Input id="title" v-model="form.title" placeholder="Product title" />
                                    <span v-if="form.errors.title" class="text-sm text-destructive">{{ form.errors.title }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="grid gap-3">
                                        <Label for="price">Price</Label>
                                        <Input id="price" type="number" step="0.01" v-model="form.price" />
                                        <span v-if="form.errors.price" class="text-sm text-destructive">{{ form.errors.price }}</span>
                                    </div>
                                    <div class="grid gap-3">
                                        <Label for="quantity">Quantity</Label>
                                        <Input id="quantity" type="number" v-model="form.quantity" />
                                        <span v-if="form.errors.quantity" class="text-sm text-destructive">{{ form.errors.quantity }}</span>
                                    </div>
                                </div>
                                <div class="grid gap-3">
                                    <Label for="description">Description</Label>
                                    <textarea id="description" v-model="form.description" rows="4"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                        placeholder="Product description..."
                                    />
                                    <span v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</span>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <!-- Image Upload Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Product Images</CardTitle>
                            <CardDescription>Manage product images. Max 2MB each.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <!-- Existing Images -->
                            <div v-if="product.images.length > 0" class="mb-4">
                                <Label class="mb-2 block">Current Images</Label>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                    <div
                                        v-for="img in product.images"
                                        :key="img.id"
                                        class="group relative aspect-square rounded-lg overflow-hidden border bg-muted"
                                        :class="{ 'opacity-30 ring-2 ring-destructive': deletedExistingIds.has(img.id) }"
                                    >
                                        <img :src="`/storage/${img.image}`" alt="Product image" class="h-full w-full object-cover" />
                                        <button
                                            v-if="!deletedExistingIds.has(img.id)"
                                            type="button"
                                            class="absolute top-1 right-1 rounded-full bg-destructive p-1 text-white opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                                            @click="markExistingForDeletion(img.id)"
                                        >
                                            <X class="h-3 w-3" />
                                        </button>
                                        <button
                                            v-else
                                            type="button"
                                            class="absolute inset-0 flex items-center justify-center text-xs font-medium text-destructive bg-background/50"
                                            @click="undoExistingDeletion(img.id)"
                                        >
                                            Undo
                                        </button>
                                    </div>
                                </div>
                                <p v-if="deletedExistingIds.size > 0" class="text-xs text-destructive mt-2">
                                    {{ deletedExistingIds.size }} image{{ deletedExistingIds.size !== 1 ? 's' : '' }} marked for deletion
                                </p>
                            </div>

                            <!-- Drop Zone for New Images -->
                            <Label class="mb-2 block">Add New Images</Label>
                            <div
                                class="relative flex flex-col items-center justify-center w-full min-h-[120px] border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                                :class="isDragging ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:border-primary/50'"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="onDrop"
                                @click="fileInput?.click()"
                            >
                                <Upload class="h-8 w-8 text-muted-foreground mb-2" />
                                <p class="text-sm text-muted-foreground font-medium">
                                    Drag &amp; drop images here, or click to browse
                                </p>
                                <p class="text-xs text-muted-foreground mt-1">
                                    JPEG, PNG, JPG, GIF, WebP
                                </p>
                                <input
                                    ref="fileInput"
                                    type="file"
                                    multiple
                                    accept="image/*"
                                    class="hidden"
                                    @change="onFileSelect"
                                />
                            </div>
                            <span v-if="form.errors.new_images" class="text-sm text-destructive mt-2 block">{{ form.errors.new_images }}</span>

                            <!-- New Image Previews -->
                            <div v-if="newPreviews.length > 0" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mt-4">
                                <div v-for="(preview, index) in newPreviews" :key="index" class="group relative aspect-square rounded-lg overflow-hidden border bg-muted">
                                    <img :src="preview" alt="New preview" class="h-full w-full object-cover" />
                                    <button
                                        type="button"
                                        class="absolute top-1 right-1 rounded-full bg-destructive p-1 text-white opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                                        @click.stop="removeNewImage(index)"
                                    >
                                        <X class="h-3 w-3" />
                                    </button>
                                </div>
                            </div>
                            <p v-if="newPreviews.length > 0" class="text-xs text-muted-foreground mt-2">
                                {{ newPreviews.length }} new image{{ newPreviews.length !== 1 ? 's' : '' }} to upload
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid auto-rows-max items-start gap-4 lg:gap-8">
                    <Card>
                        <CardHeader>
                            <CardTitle>Organization</CardTitle>
                        </CardHeader>
                        <CardContent class="grid gap-6">
                            <div class="grid gap-3">
                                <Label for="brand">Brand</Label>
                                <Select v-model="form.brand_id">
                                    <SelectTrigger id="brand">
                                        <SelectValue placeholder="Select a brand" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="brand in brands" :key="brand.id" :value="String(brand.id)">
                                            {{ brand.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="grid gap-3">
                                <Label for="category">Category</Label>
                                <Select v-model="form.category_id">
                                    <SelectTrigger id="category">
                                        <SelectValue placeholder="Select a category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                            {{ cat.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Checkbox id="published" :model-value="form.published" @update:model-value="(v: boolean | 'indeterminate') => { if (typeof v === 'boolean') form.published = v }" />
                                <Label for="published" class="text-sm font-medium leading-none">Published</Label>
                            </div>
                        </CardContent>
                        <CardFooter class="border-t px-6 py-4">
                            <Button @click="submit" :disabled="form.processing" class="w-full">Save Changes</Button>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
