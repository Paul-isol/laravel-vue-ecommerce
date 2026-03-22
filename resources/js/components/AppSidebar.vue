<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, ListTree, Tags, ShoppingBag, ShoppingCart, Users } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { toUrl } from '@/lib/utils';
import { dashboard } from '@/routes';
import admin from '@/routes/admin';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: admin.dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Shop',
        href: admin.dashboard(),
        icon: ShoppingBag,
        children: [
            {
                title: 'Products',
                href: admin.products.index(),
                icon: LayoutGrid,
            },
            {
                title: 'Categories',
                href: admin.categories.index(),
                icon: ListTree,
            },
            {
                title: 'Brands',
                href: admin.brands.index(),
                icon: Tags,
            }
        ]
    },
    {
        title: 'Sales',
        href: admin.dashboard(),
        icon: ShoppingCart,
        children: [
            {
                title: 'Orders',
                href: admin.orders.index(),
                icon: ShoppingCart,
            }
        ]
    },
    {
        title: 'Customers',
        href: admin.dashboard(),
        icon: Users,
        children: [
            {
                title: 'Users',
                href: admin.users.index(),
                icon: Users,
            }
        ]
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="toUrl(dashboard())">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
