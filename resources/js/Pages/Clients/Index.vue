<template>
    <AuthentecatedLayout>
        <!-- Breadcrumb Area -->
        <section
            class="breadcumb-area bg-img d-flex align-items-center justify-content-center"
            style="background-image: url(/img/bg-img/bg-9.jpg)"
        >
            <div class="bradcumbContent">
                <h2>Clients Management</h2>
            </div>
        </section>

        <!-- Clients Table Area -->
        <section class="elements-area section-padding-100-0">
            <div class="container">
                <div class="row justify-content-end mb-50">
                    <div class="col-12 col-md-4 text-right" v-if="can.create">
                        <router-link
                            :to="{ name: 'clients.create' }"
                            class="btn palatin-btn"
                        >
                            Add New Client
                        </router-link>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="elements-title mb-70">
                            <h2>Clients List</h2>
                        </div>

                        <div class="palatin-tabs-content">
                            <div class="tab-content">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Country</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="client in clients.data"
                                                :key="client.id"
                                            >
                                                <td>{{ client.id }}</td>
                                                <td>
                                                    <div
                                                        class="d-flex align-items-center"
                                                    >
                                                        <img
                                                            :src="
                                                                client.avatar_image
                                                                    ? '/storage/' +
                                                                      client.avatar_image
                                                                    : '/img/core-img/default-avatar.png'
                                                            "
                                                            class="rounded-circle mr-3"
                                                            width="40"
                                                            height="40"
                                                            alt="Avatar"
                                                        />
                                                        {{ client.user.name }}
                                                    </div>
                                                </td>
                                                <td>{{ client.user.email }}</td>
                                                <td>
                                                    {{ client.phone_number }}
                                                </td>
                                                <td>{{ client.country }}</td>
                                                <td>
                                                    <span
                                                        :class="[
                                                            'badge',
                                                            client.approved_at
                                                                ? 'badge-success'
                                                                : 'badge-warning',
                                                        ]"
                                                    >
                                                        {{
                                                            client.approved_at
                                                                ? "Approved"
                                                                : "Pending"
                                                        }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <router-link
                                                        :to="{
                                                            name: 'clients.show',
                                                            params: {
                                                                id: client.id,
                                                            },
                                                        }"
                                                        class="btn palatin-btn btn-3 btn-sm mr-2"
                                                    >
                                                        View
                                                    </router-link>
                                                    <button
                                                        v-if="
                                                            can.approve &&
                                                            !client.approved_at
                                                        "
                                                        @click="
                                                            approveClient(
                                                                client,
                                                            )
                                                        "
                                                        class="btn palatin-btn btn-sm"
                                                    >
                                                        Approve
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="pagination-area mt-70">
                                            <nav aria-label="Page navigation">
                                                <ul class="pagination">
                                                    <li
                                                        class="page-item"
                                                        :class="{
                                                            disabled:
                                                                !clients.prev_page_url,
                                                        }"
                                                    >
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                            @click.prevent="
                                                                changePage(
                                                                    clients.current_page -
                                                                        1,
                                                                )
                                                            "
                                                        >
                                                            Previous
                                                        </a>
                                                    </li>
                                                    <li
                                                        class="page-item"
                                                        v-for="page in clients.last_page"
                                                        :key="page"
                                                        :class="{
                                                            active:
                                                                page ===
                                                                clients.current_page,
                                                        }"
                                                    >
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                            @click.prevent="
                                                                changePage(page)
                                                            "
                                                        >
                                                            {{ page }}
                                                        </a>
                                                    </li>
                                                    <li
                                                        class="page-item"
                                                        :class="{
                                                            disabled:
                                                                !clients.next_page_url,
                                                        }"
                                                    >
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                            @click.prevent="
                                                                changePage(
                                                                    clients.current_page +
                                                                        1,
                                                                )
                                                            "
                                                        >
                                                            Next
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthentecatedLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import AuthentecatedLayout from "@/Layouts/AuthenticatedLayout.vue";
// Define props from Laravel controller
const props = defineProps({
    clients: {
        type: Object,
        required: true,
    },
    can: {
        type: Object,
        default: () => ({}),
    },
});

// Client approval function
const approveClient = (client) => {
    if (confirm("Are you sure you want to approve this client?")) {
        router.post(
            route("clients.approve", client.id),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Update the local client data
                    client.approved_at = new Date().toISOString();
                },
            },
        );
    }
};

// Pagination function
const changePage = (page) => {
    router.get(
        route("clients.index"),
        { page },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};
</script>

<style lang="scss" scoped>
.breadcumb-area {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-size: cover;
    background-position: center;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;

    th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
        background-color: #f8f9fa;
        padding: 12px;
    }

    td {
        padding: 12px;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
    }
}

.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;

    &-success {
        background-color: #28a745;
        color: white;
    }

    &-warning {
        background-color: #ffc107;
        color: #212529;
    }
}

.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: 0.25rem;

    .page-item {
        &.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #cb8670;
            border-color: #cb8670;
        }

        &.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: auto;
            background-color: #fff;
            border-color: #dee2e6;
        }
    }

    .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #cb8670;
        background-color: #fff;
        border: 1px solid #dee2e6;

        &:hover {
            z-index: 2;
            color: #fff;
            text-decoration: none;
            background-color: #cb8670;
            border-color: #cb8670;
        }
    }
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}
</style>
