<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Button } from "@/Components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
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
    floors: {
        type: Array,
        required: true,
    },
});

const isSubmitting = ref(false);

const form = useForm({
    price: "",
    capacity: "",
    manager_id: "",
    floor_id: "",
});

// Form validation
const validateForm = () => {
    let isValid = true;

    if (!form.price || form.price < 0) {
        form.errors.price = "Valid price is required";
        isValid = false;
    }

    if (!form.capacity || form.capacity < 1) {
        form.errors.capacity = "Valid capacity is required";
        isValid = false;
    }

    if (!form.manager_id) {
        form.errors.manager_id = "Manager is required";
        isValid = false;
    }

    if (!form.floor_id) {
        form.errors.floor_id = "Floor is required";
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
    form.post(route("rooms.store"), {
        onSuccess: () => {
            toast({
                title: "Success",
                description: "Room created successfully",
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

const selectedManager = computed(() => {
    return (
        props.managers.find((m) => m.id === form.manager_id)?.name ||
        "Select a manager"
    );
});

const selectedFloor = computed(() => {
    return (
        props.floors.find((f) => f.id === form.floor_id)?.name ||
        "Select a floor"
    );
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Room
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Room Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="price">Price</Label>
                                    <Input
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        min="0"
                                        :class="{
                                            'border-red-500': form.errors.price,
                                        }"
                                        placeholder="Enter room price"
                                    />
                                    <span
                                        v-if="form.errors.price"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.price }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <Label for="capacity">Capacity</Label>
                                    <Input
                                        id="capacity"
                                        v-model="form.capacity"
                                        type="number"
                                        min="1"
                                        :class="{
                                            'border-red-500':
                                                form.errors.capacity,
                                        }"
                                        placeholder="Enter room capacity"
                                    />
                                    <span
                                        v-if="form.errors.capacity"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.capacity }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
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
                                            <SelectValue>{{
                                                selectedManager
                                            }}</SelectValue>
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

                                <div class="space-y-2">
                                    <Label for="floor">Floor</Label>
                                    <Select v-model="form.floor_id">
                                        <SelectTrigger
                                            :class="{
                                                'border-red-500':
                                                    form.errors.floor_id,
                                            }"
                                            class="w-full"
                                        >
                                            <SelectValue>{{
                                                selectedFloor
                                            }}</SelectValue>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="floor in floors"
                                                :key="floor.id"
                                                :value="floor.id"
                                            >
                                                {{ floor.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <span
                                        v-if="form.errors.floor_id"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.floor_id }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="
                                        $inertia.visit(route('rooms.index'))
                                    "
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing || isSubmitting"
                                >
                                    {{
                                        form.processing
                                            ? "Creating..."
                                            : "Create Room"
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
