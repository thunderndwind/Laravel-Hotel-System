<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Button } from "@/Components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { toast } from "@/Components/ui/use-toast";

const props = defineProps({
    manager: Object,
});

const isSubmitting = ref(false);

const form = useForm({
    name: props.manager.name,
    email: props.manager.email,
    national_id: props.manager.national_id,
    phone_number: props.manager.phone_number,
    avatar_image: null,
});

const validateForm = () => {
    let isValid = true;

    if (!form.name || form.name.trim() === "") {
        form.errors.name = "Name is required";
        isValid = false;
    }

    if (!form.email || form.email.trim() === "") {
        form.errors.email = "Email is required";
        isValid = false;
    }

    if (!form.national_id || form.national_id.trim() === "") {
        form.errors.national_id = "National ID is required";
        isValid = false;
    } else if (form.national_id.length < 15) {
        form.errors.national_id = "National ID must be at least 15 characters";
        isValid = false;
    } else if (form.national_id.length > 25) {
        form.errors.national_id = "National ID must not exceed 25 characters";
        isValid = false;
    }

    if (!form.phone_number || form.phone_number.trim() === "") {
        form.errors.phone_number = "Phone number is required";
        isValid = false;
    }

    return isValid;
};

function handleFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            form.errors.avatar_image = "Image size must not exceed 2MB";
            event.target.value = "";
            return;
        }
        form.avatar_image = file;
    }
}

const submit = () => {
    if (!validateForm()) {
        toast({
            title: "Validation Error",
            description: "Please check the form for errors",
            variant: "destructive",
        });
        return;
    }

    isSubmitting.value = true;
    form.patch(route("managers.update", props.manager.id), {
        onSuccess: () => {
            toast({
                title: "Success",
                description: "Manager updated successfully",
            });
            isSubmitting.value = false;
        },
        onError: () => {
            toast({
                title: "Error",
                description: "Please check the form for errors",
                variant: "destructive",
            });
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Manager Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    :class="{
                                        'border-red-500': form.errors.name,
                                    }"
                                    placeholder="Enter manager name"
                                />
                                <div
                                    v-if="form.errors.name"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    :class="{
                                        'border-red-500': form.errors.email,
                                    }"
                                    placeholder="Enter email address"
                                />
                                <div
                                    v-if="form.errors.email"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="national_id">National ID</Label>
                                <Input
                                    id="national_id"
                                    v-model="form.national_id"
                                    type="text"
                                    :class="{
                                        'border-red-500':
                                            form.errors.national_id,
                                    }"
                                    placeholder="Enter national ID (15-25 characters)"
                                />
                                <div
                                    v-if="form.errors.national_id"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.national_id }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="phone_number">Phone Number</Label>
                                <Input
                                    id="phone_number"
                                    v-model="form.phone_number"
                                    type="text"
                                    :class="{
                                        'border-red-500':
                                            form.errors.phone_number,
                                    }"
                                    placeholder="Enter phone number"
                                />
                                <div
                                    v-if="form.errors.phone_number"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.phone_number }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="avatar_image">Avatar Image</Label>
                                <Input
                                    id="avatar_image"
                                    type="file"
                                    accept="image/*"
                                    @change="handleFileChange"
                                    :class="{
                                        'border-red-500':
                                            form.errors.avatar_image,
                                    }"
                                />
                                <div
                                    v-if="form.errors.avatar_image"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.avatar_image }}
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="
                                        $inertia.visit(route('managers.index'))
                                    "
                                >
                                    Cancel
                                </Button>
                                <Button type="submit" :disabled="isSubmitting">
                                    {{
                                        isSubmitting
                                            ? "Updating..."
                                            : "Update Manager"
                                    }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
