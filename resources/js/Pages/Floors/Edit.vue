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
    floor: {
        type: Object,
        required: true,
    },
    managers: {
        type: Array,
        required: true,
    },
});

const isSubmitting = ref(false);

const form = useForm({
    name: props.floor.name,
    manager_id: props.floor.manager_id,
});

// Modify the currentManager computed property
const currentManager = computed(() => {
    const manager = props.managers.find((m) => m.id === form.manager_id);
    return manager ? manager.name : "Select a manager";
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
    form.put(route("floors.update", props.floor.id), {
        onSuccess: () => {
            toast({
                title: "Success",
                description: "Floor updated successfully",
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
                Edit Floor
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit Floor Details</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="name">Floor Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    :disabled="form.processing || isSubmitting"
                                    :class="{
                                        'border-red-500': form.errors.name,
                                    }"
                                    placeholder="Enter floor name"
                                />
                                <span
                                    v-if="form.errors.name"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.name }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="manager">Floor Manager</Label>
                                <Select v-model="form.manager_id">
                                    <SelectTrigger
                                        :disabled="
                                            form.processing || isSubmitting
                                        "
                                        :class="{
                                            'border-red-500':
                                                form.errors.manager_id,
                                        }"
                                        class="w-full"
                                    >
                                        <SelectValue>
                                            {{ currentManager }}
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
                                <span
                                    v-if="form.errors.manager_id"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.manager_id }}
                                </span>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    :disabled="form.processing || isSubmitting"
                                    @click="
                                        $inertia.visit(route('floors.index'))
                                    "
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing || isSubmitting"
                                >
                                    Update Floor
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
