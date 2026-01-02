<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    goal: Object,
});

const progress = computed(() => {
    if (props.goal.type === 'yes_no') {
        return props.goal.is_completed ? 100 : 0;
    }
    if (props.goal.type === 'percentage') {
        return parseFloat(props.goal.current_value) || 0;
    }
    if (props.goal.target_value > 0) {
        return Math.min(100, (props.goal.current_value / props.goal.target_value) * 100);
    }
    return 0;
});

const progressText = computed(() => {
    if (props.goal.type === 'yes_no') {
        return props.goal.is_completed ? 'Completed' : 'Not completed';
    }
    if (props.goal.type === 'percentage') {
        return `${props.goal.current_value}%`;
    }
    if (props.goal.type === 'money') {
        return `${props.goal.currency} ${props.goal.current_value} / ${props.goal.target_value}`;
    }
    return `${props.goal.current_value} / ${props.goal.target_value} ${props.goal.unit || ''}`;
});

const archive = () => {
    if (confirm('Are you sure you want to archive this goal?')) {
        router.post(route('goals.archive', props.goal.id));
    }
};

const deleteGoal = () => {
    if (confirm('Are you sure you want to delete this goal? This cannot be undone.')) {
        router.delete(route('goals.destroy', props.goal.id));
    }
};
</script>

<template>
    <Head :title="goal.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('goals.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ goal.title }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow-sm space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-500">{{ progressText }}</span>
                            <span class="text-2xl font-bold">{{ Math.round(progress) }}%</span>
                        </div>
                        <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                                :style="{ width: `${progress}%` }"
                            ></div>
                        </div>
                    </div>

                    <div v-if="goal.description" class="text-gray-600">
                        {{ goal.description }}
                    </div>

                    <div class="flex flex-wrap gap-2 text-sm">
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">
                            {{ goal.category.replace('_', ' & ') }}
                        </span>
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600">
                            {{ goal.type.replace('_', '/') }}
                        </span>
                    </div>

                    <div v-if="goal.log_entries?.length" class="border-t pt-6">
                        <h3 class="font-medium text-gray-900 mb-4">Recent Activity</h3>
                        <div class="space-y-3">
                            <div
                                v-for="entry in goal.log_entries"
                                :key="entry.id"
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="text-gray-600">
                                    {{ entry.note || `+${entry.value}` }}
                                </span>
                                <span class="text-gray-400">
                                    {{ new Date(entry.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-6 border-t">
                        <Link
                            :href="route('goals.edit', goal.id)"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                        >
                            Edit
                        </Link>
                        <button
                            @click="archive"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                        >
                            Archive
                        </button>
                        <button
                            @click="deleteGoal"
                            class="px-4 py-2 text-red-600 hover:text-red-700 transition"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
