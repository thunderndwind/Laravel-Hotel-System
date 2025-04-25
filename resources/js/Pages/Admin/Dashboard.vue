<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div
                        v-for="(stat, key) in stats"
                        :key="key"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <div class="text-gray-500 text-sm">
                            {{ formatLabel(key) }}
                        </div>
                        <div class="text-3xl font-bold text-gray-900">
                            {{ stat }}
                        </div>
                    </div>
                </div>

                <!-- Receptionists Management Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-900">
                                Manage Receptionists
                            </h2>
                            <button
                                @click="openModal()"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                Add Receptionist
                            </button>
                        </div>

                        <!-- Receptionists Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Name
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Email
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Status
                                        </th>

                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="receptionist in receptionists"
                                        :key="receptionist.id"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{
                                                receptionist.user
                                                    ? receptionist.user.name
                                                    : ""
                                            }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{
                                                receptionist.user
                                                    ? receptionist.user.email
                                                    : ""
                                            }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    receptionist.user
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-yellow-100 text-yellow-800',
                                                ]"
                                            >
                                                {{
                                                    receptionist.user
                                                        ? "Active"
                                                        : "Pending"
                                                }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <div
                                                class="flex items-center space-x-3"
                                            >
                                                <button
                                                    @click="
                                                        editReceptionist(
                                                            receptionist,
                                                        )
                                                    "
                                                    class="text-indigo-600 hover:text-indigo-900 me-3"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    @click="
                                                        deleteReceptionist(
                                                            receptionist.id,
                                                        )
                                                    "
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ form.id ? "Edit Receptionist" : "Add New Receptionist" }}
                </h2>

                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <Label for="national_id">National ID</Label>
                        <Input
                            id="national_id"
                            v-model="form.national_id"
                            type="text"
                            required
                            class="mt-1 block w-full"
                        />
                        <InputError
                            :message="form.errors.national_id"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <Label for="phone_number">Phone Number</Label>
                        <Input
                            id="phone_number"
                            v-model="form.phone_number"
                            type="text"
                            required
                            class="mt-1 block w-full"
                        />
                        <InputError
                            :message="form.errors.phone_number"
                            class="mt-2"
                        />
                    </div>

                    <div v-if="!form.id">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            class="mt-1 block w-full"
                        />
                        <InputError
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                    <div v-if="!form.id">
                        <Label for="avatar_image">Avatar Image</Label>
                        <Input
                            id="avatar_image"
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                            class="mt-1 block w-full"
                        />
                        <InputError
                            :message="form.errors.avatar_image"
                            class="mt-2"
                        />

                        <!-- Preview image if available -->
                        <div v-if="form.avatar_image" class="mt-2">
                            <img
                                :src="form.avatar_image"
                                alt="Avatar preview"
                                class="h-20 w-20 object-cover rounded-full"
                            />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing"
                            >Save</Button
                        >
                        <Button
                            type="button"
                            @click="closeModal"
                            variant="secondary"
                            >Cancel</Button
                        >
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useForm, router } from "@inertiajs/vue3"; // Add router import
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    stats: Object,
    receptionists: Array,
});
console.log("Receptionists:", props.receptionists); // Debugging line
const showModal = ref(false);
const form = useForm({
    id: "",
    name: "",
    email: "",
    password: "",
    national_id: "",
    phone_number: "",
    avatar_image: "",
});

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            form.avatar_image = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const formatLabel = (key) => {
    return key
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

const openModal = () => {
    form.reset();
    showModal.value = true;
};

const closeModal = () => {
    form.reset();
    showModal.value = false;
};

const editReceptionist = (receptionist) => {
    form.id = receptionist.id;
    form.name = receptionist.user?.name || "";
    form.email = receptionist.user?.email || "";
    form.national_id = receptionist.national_id;
    form.phone_number = receptionist.phone_number;
    form.avatar_image = receptionist.avatar_image || "";
    showModal.value = true;
};

const submitForm = () => {
    console.log("Submitting form:", form.data());

    if (form.id) {
        form.put(route("receptionists.update", form.id), {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Update successful");
                showModal.value = false;
                form.reset();
                window.location.reload();
            },
            onError: (errors) => {
                console.error("Update failed:", errors);
            },
        });
    } else {
        form.post(route("receptionists.store"), {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Create successful");
                showModal.value = false;
                form.reset();
                window.location.reload();
            },
            onError: (errors) => {
                console.error("Create failed:", errors);
            },
        });
    }
};

const deleteReceptionist = (id) => {
    if (confirm("Are you sure you want to delete this receptionist?")) {
        router.delete(route("receptionists.destroy", id), {
            preserveScroll: true, // Add this
            onSuccess: () => {
                window.location.reload(); // Refresh after deletion
            },
        });
    }
};
</script>
