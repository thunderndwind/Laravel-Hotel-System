<script setup>
import { useForm } from "@inertiajs/vue3";
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
    floor: {
        type: Object,
        required: true,
    },
    managers: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: props.floor.name,
    manager_id: props.floor.manager_id,
});

const submit = () => {
    form.put(route("floors.update", props.floor.id), {
        onSuccess: () => {
            toast({
                title: "Success",
                description: "Floor updated successfully",
            });
        },
        onError: () => {
            toast({
                title: "Error",
                description: "Something went wrong",
                variant: "destructive",
            });
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
                                    :disabled="form.processing"
                                    :class="{
                                        'border-red-500': form.errors.name,
                                    }"
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
                                    <SelectTrigger :disabled="form.processing">
                                        <SelectValue>
                                            {{
                                                managers.find(
                                                    (m) =>
                                                        m.id ===
                                                        form.manager_id,
                                                )?.name
                                            }}
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
                                    :disabled="form.processing"
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
                                    Save Changes
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
