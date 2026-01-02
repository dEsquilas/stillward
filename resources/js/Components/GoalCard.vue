<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    goal: Object,
    categoryColor: String,
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
</script>

<template>
    <Link
        :href="route('goals.show', goal.id)"
        class="block p-4 bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition"
    >
        <div class="flex items-start justify-between mb-3">
            <h4 class="font-medium text-gray-900 line-clamp-2">
                {{ goal.title }}
            </h4>
            <span
                v-if="goal.is_completed"
                class="ml-2 px-2 py-0.5 text-xs font-medium text-green-700 bg-green-100 rounded-full"
            >
                Done
            </span>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">{{ progressText }}</span>
                <span class="font-medium" :style="{ color: categoryColor }">
                    {{ Math.round(progress) }}%
                </span>
            </div>

            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                <div
                    class="h-full rounded-full transition-all duration-300"
                    :style="{
                        width: `${progress}%`,
                        backgroundColor: categoryColor,
                    }"
                ></div>
            </div>
        </div>
    </Link>
</template>
