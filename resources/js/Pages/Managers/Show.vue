<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    manager: Object,
    can: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold"
                            >Manager Details</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                <img
                                    :src="
                                        manager.avatar_image ||
                                        '/storage/avatars/Default.png'
                                    "
                                    alt="Avatar"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"
                                />
                            </div>
                            <div class="flex-grow space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <h3
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Name
                                        </h3>
                                        <p class="text-lg">
                                            {{ manager.user?.name }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <h3
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Email
                                        </h3>
                                        <p class="text-lg">
                                            {{ manager.user?.email }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <h3
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            National ID
                                        </h3>
                                        <p class="text-lg">
                                            {{ manager.national_id }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <h3
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Phone Number
                                        </h3>
                                        <p class="text-lg">
                                            {{ manager.phone_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <Link :href="route('managers.index')">
                                <Button variant="outline">
                                    Back to List
                                </Button>
                            </Link>

                            <Link
                                v-if="can && can.update"
                                :href="route('managers.edit', manager.id)"
                            >
                                <Button variant="primary">
                                    Edit Manager
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
