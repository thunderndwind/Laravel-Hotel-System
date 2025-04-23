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
    room: {
        type: Object,
        required: true,
    },
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
    price: props.room.price,
    capacity: props.room.capacity,
    manager_id: props.room.manager_id,
    floor_id: props.room.floor_id,
});

// Computed properties for selected values
const selectedManager = computed(() => {
    const manager = props.managers.find((m) => m.id === form.manager_id);
    return manager ? manager.name : "Select a manager";
});

const selectedFloor = computed(() => {
    const floor = props.floors.find((f) => f.id === form.floor_id);
    return floor ? floor.name : "Select a floor";
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
    form.put(route("rooms.update", props.room.id), {
        onSuccess: () => {
            toast({
                title: "Success",
                description: "Room updated successfully",
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
                Edit Room
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit Room Details</CardTitle>
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
                                        :disabled="
                                            form.processing || isSubmitting
                                        "
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
                                        :disabled="
                                            form.processing || isSubmitting
                                        "
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
                                            :disabled="
                                                form.processing || isSubmitting
                                            "
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
                                            :disabled="
                                                form.processing || isSubmitting
                                            "
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
                                    :disabled="form.processing || isSubmitting"
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
                                            ? "Updating..."
                                            : "Update Room"
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
