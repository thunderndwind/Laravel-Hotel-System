<script setup>
import { ref, computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import {
    Bars3Icon,
    XMarkIcon,
    ChevronDownIcon,
    UsersIcon,
    UserGroupIcon,
    BuildingOfficeIcon,
    HomeIcon,
    ClipboardDocumentListIcon,
    UserIcon,
} from "@heroicons/vue/24/outline";

const showingNavigationDropdown = ref(false);
const showingSidebar = ref(true);
const page = usePage();

const userRole = computed(() => {
    const user = page.props.auth?.user;
    return user?.roles?.[0]?.name || "";
});

// Update navigation items to include proper route checks
const navigationItems = computed(() => {
    const items = [
        {
            name: "Dashboard",
            route: route(userRole.value.toLowerCase() + ".dashboard"),
            icon: HomeIcon,
            roles: ["Admin", "Manager", "Receptionist"],
        },
    ];

    if (!userRole.value) return items;

    // Admin items
    if (userRole.value === "Admin") {
        items.push({
            name: "Manage Managers",
            route: route("managers.index"),
            icon: UserGroupIcon,
            roles: ["Admin"],
        });
    }

    // Admin and Manager items
    if (["Admin", "Manager"].includes(userRole.value)) {
        items.push(
            {
                name: "Manage Receptionists",
                route: route("receptionists.index"),
                icon: UsersIcon,
                roles: ["Admin", "Manager"],
            },
            {
                name: "Manage Floors",
                route: route("floors.index"),
                icon: BuildingOfficeIcon,
                roles: ["Admin", "Manager"],
            },
            {
                name: "Manage Rooms",
                route: route("rooms.index"),
                icon: HomeIcon,
                roles: ["Admin", "Manager"],
            },
        );
    }

    // Items for all staff roles
    if (["Admin", "Manager", "Receptionist"].includes(userRole.value)) {
        items.push({
            name: "Manage Clients",
            route: route("clients.index"),
            icon: UserGroupIcon,
            roles: ["Admin", "Manager", "Receptionist"],
        });
    }

    // Receptionist specific items
    if (userRole.value === "Receptionist") {
        items.push(
            {
                name: "Pending Clients",
                route: route("receptionist.pendingClients"),
                icon: UserIcon,
                roles: ["Receptionist"],
            },
            {
                name: "Approved Clients",
                route: route("receptionist.approvedClients"),
                icon: UserGroupIcon,
                roles: ["Receptionist"],
            },
        );
    }

    return items;
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside
                :class="[
                    'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transition-transform duration-150 ease-in lg:relative lg:transform-none',
                    { '-translate-x-full': !showingSidebar },
                ]"
            >
                <!-- Sidebar Header -->
                <div
                    class="flex h-16 items-center justify-between border-b px-6"
                >
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationLogo class="h-8 w-8" />
                        <span class="ml-2 text-xl font-semibold"
                            >Hotel System</span
                        >
                    </Link>
                    <button
                        @click="showingSidebar = !showingSidebar"
                        class="lg:hidden"
                    >
                        <XMarkIcon class="h-6 w-6" />
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-6 px-4">
                    <div class="space-y-1">
                        <template
                            v-for="item in navigationItems"
                            :key="item.name"
                        >
                            <Link
                                v-if="item.roles.includes(userRole)"
                                :href="item.route"
                                class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-200"
                                :class="{
                                    'bg-gray-100 text-gray-900':
                                        route().current(item.route),
                                    'text-gray-600 hover:bg-gray-50 hover:text-gray-900':
                                        !route().current(item.route),
                                }"
                            >
                                <component
                                    :is="item.icon"
                                    class="mr-3 h-5 w-5"
                                    aria-hidden="true"
                                />
                                {{ item.name }}
                            </Link>
                        </template>
                    </div>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <div class="flex flex-1 flex-col overflow-hidden">
                <!-- Top Navigation -->
                <header class="bg-white shadow">
                    <div
                        class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8"
                    >
                        <!-- Mobile menu button-->
                        <button
                            @click="showingSidebar = !showingSidebar"
                            class="text-gray-500 focus:outline-none lg:hidden"
                        >
                            <Bars3Icon class="h-6 w-6" />
                        </button>

                        <!-- User Dropdown -->
                        <div class="flex items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        class="flex items-center space-x-2 text-sm"
                                    >
                                        <img
                                            :src="
                                                page.props.auth.user.avatar ||
                                                '/default-avatar.png'
                                            "
                                            class="h-8 w-8 rounded-full"
                                        />
                                        <span>{{
                                            page.props.auth.user.name
                                        }}</span>
                                        <ChevronDownIcon class="h-4 w-4" />
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    <!-- Page Heading -->
                    <header class="bg-white shadow" v-if="$slots.header">
                        <div class="px-4 py-6 sm:px-6 lg:px-8">
                            <slot name="header" />
                        </div>
                    </header>

                    <div class="py-6">
                        <div class="mx-auto px-4 sm:px-6 lg:px-8">
                            <slot />
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add any custom styles here */
.router-link-active {
    @apply bg-gray-100 text-gray-900;
}
</style>
