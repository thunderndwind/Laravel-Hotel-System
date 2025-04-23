<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Button } from "@/Components/ui/button";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { toast } from "@/Components/ui/use-toast";

const props = defineProps({
    managers: {
        type: Array,
        required: true,
    },
});

const isSubmitting = ref(false);

const form = useForm({
    name: "",
    manager_id: "",
});

// Form validation
const validateForm = () => {
    let isValid = true;

    if (!form.name || form.name.trim() === "") {
        form.errors.name = "Floor name is required";
        isValid = false;
    } else if (form.name.length > 255) {
        form.errors.name = "Floor name must be less than 255 characters";
        isValid = false;
    }

    if (!form.manager_id) {
        form.errors.manager_id = "Manager is required";
        isValid = false;
    }

    return isValid;
};

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
    form.post(route("floors.store"), {
        onSuccess: () => {
            toast({
                title: "Success",
                description: "Floor created successfully",
            });
            form.reset();
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

// Add a computed property to find the selected manager
const selectedManager = computed(() => {
    return (
        props.managers.find((manager) => manager.id === form.manager_id)
            ?.name || "Select a manager"
    );
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Floor
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Floor Information</CardTitle>
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
                                    placeholder="Enter floor name"
                                />
                                <div
                                    v-if="form.errors.name"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="manager">Manager</Label>
                                <Select v-model="form.manager_id">
                                    <SelectTrigger
                                        :class="{
                                            'border-red-500':
                                                form.errors.manager_id,
                                        }"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select a manager"
                                        >
                                            {{ selectedManager }}
                                        </SelectValue>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="manager in managers"
                                            :key="manager.id"
                                            :value="manager.id"
                                        >
                                            {{ manager.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div
                                    v-if="form.errors.manager_id"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.manager_id }}
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="
                                        $inertia.visit(route('floors.index'))
                                    "
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    {{
                                        form.processing
                                            ? "Creating..."
                                            : "Create Floor"
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
