<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import {
    LayoutDashboard,
    Package,
    FolderTree,
    Warehouse,
    Boxes,
    PackagePlus,
    Receipt,
    Truck,
    ShoppingBag,
} from "@lucide/vue";
import AppLogo from "@/components/AppLogo.vue";
import NavFooter from "@/components/NavFooter.vue";
import NavMain from "@/components/NavMain.vue";
import NavUser from "@/components/NavUser.vue";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";
import type { NavItem } from "@/types";

const mainNavItems: NavItem[] = [
    {
        title: "Dashboard",
        href: "/admin/dashboard",
        icon: LayoutDashboard,
    },
    {
        title: "Products",
        href: "/admin/products",
        icon: Package,
    },
    {
        title: "Categories",
        href: "/admin/categories",
        icon: FolderTree,
    },
    {
        title: "Current Stock",
        href: "/admin/inventory",
        icon: Warehouse,
    },
    {
        title: "Inventory Items",
        href: "/admin/inventory/items",
        icon: Boxes,
    },
    {
        title: "Add Stock",
        href: "/admin/inventory/add",
        icon: PackagePlus,
    },
    {
        title: "Receipts",
        href: "/admin/inventory/receipts",
        icon: Receipt,
    },
    {
        title: "Suppliers",
        href: "/admin/suppliers",
        icon: Truck,
    },
    {
        title: "Sales",
        href: "/admin/sales",
        icon: ShoppingBag,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
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
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>