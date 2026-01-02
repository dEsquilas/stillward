<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    goals: Object,
    categories: Array,
});

const restore = (goalId) => {
    router.post(route('goals.restore', goalId));
};
</script>

<template>
    <Head title="Archived Goals" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('goals.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Archived Goals
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="Object.keys(goals).length === 0" class="text-center py-12">
                    <p class="text-gray-500">No archived goals.</p>
                </div>

                <div v-else class="space-y-8">
                    <div v-for="category in categories" :key="category.value">
                        <div v-if="goals[category.value]?.length" class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700">
                                {{ category.label }}
                            </h3>

                            <div class="space-y-2">
                                <div
                                    v-for="goal in goals[category.value]"
                                    :key="goal.id"
                                    class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm"
                                >
                                    <span class="text-gray-600">{{ goal.title }}</span>
                                    <button
                                        @click="restore(goal.id)"
                                        class="text-sm text-indigo-600 hover:text-indigo-800"
                                    >
                                        Restore
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
