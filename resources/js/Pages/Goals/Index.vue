<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GoalCard from '@/Components/GoalCard.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    goals: Object,
    archivedCount: Number,
    categories: Array,
});

const getCategoryInfo = (categoryValue, categories) => {
    return categories.find(c => c.value === categoryValue) || {};
};
</script>

<template>
    <Head title="Goals" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Goals
                </h2>
                <Link
                    :href="route('goals.create')"
                    class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition"
                >
                    New Goal
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="Object.keys(goals).length === 0" class="text-center py-12">
                    <p class="text-gray-500 mb-4">No goals yet. Create your first goal to get started!</p>
                    <Link
                        :href="route('goals.create')"
                        class="px-6 py-3 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-700 transition"
                    >
                        Create Goal
                    </Link>
                </div>

                <div v-else class="space-y-8">
                    <div v-for="category in categories" :key="category.value">
                        <div v-if="goals[category.value]?.length" class="space-y-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-3 h-3 rounded-full"
                                    :style="{ backgroundColor: category.color }"
                                ></div>
                                <h3 class="text-lg font-semibold text-gray-700">
                                    {{ category.label }}
                                </h3>
                                <span class="text-sm text-gray-400">
                                    ({{ goals[category.value].length }})
                                </span>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <GoalCard
                                    v-for="goal in goals[category.value]"
                                    :key="goal.id"
                                    :goal="goal"
                                    :category-color="category.color"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="archivedCount > 0" class="mt-8 pt-8 border-t">
                    <Link
                        :href="route('goals.archived')"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        View {{ archivedCount }} archived goal{{ archivedCount > 1 ? 's' : '' }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
