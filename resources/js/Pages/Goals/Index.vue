<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    goals: Object,
    archivedCount: Number,
    categories: Array,
});

const processing = ref(null);

const totalGoals = computed(() => Object.values(props.goals).flat().length);
const completedGoals = computed(
    () =>
        Object.values(props.goals)
            .flat()
            .filter((g) => g.is_completed).length
);
const overallProgress = computed(() => {
    if (totalGoals.value === 0) return 0;
    return Math.round((completedGoals.value / totalGoals.value) * 100);
});

const getProgress = (goal) => {
    if (goal.type === 'yes_no') return goal.is_completed ? 100 : 0;
    if (goal.type === 'percentage') return parseFloat(goal.current_value) || 0;
    if (goal.target_value > 0) return Math.min(100, (goal.current_value / goal.target_value) * 100);
    return 0;
};

const getProgressText = (goal) => {
    if (goal.type === 'yes_no') return goal.is_completed ? 'Done' : 'Pending';
    if (goal.type === 'percentage') return `${goal.current_value}%`;
    if (goal.type === 'money') return `${goal.currency} ${goal.current_value}/${goal.target_value}`;
    return `${goal.current_value}/${goal.target_value}`;
};

const quickLog = (goal, e) => {
    e.preventDefault();
    e.stopPropagation();

    if (processing.value) return;
    processing.value = goal.id;

    const value = goal.type === 'yes_no' ? (goal.is_completed ? 0 : 1) : 1;

    router.post(
        route('goals.log', goal.id),
        { value },
        {
            preserveScroll: true,
            onFinish: () => {
                processing.value = null;
            },
        }
    );
};

const canQuickLog = (goal) => {
    return goal.type === 'counter' || goal.type === 'yes_no';
};
</script>

<template>
    <Head title="Goals" />

    <AuthenticatedLayout>
        <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Goals</h1>
                        <p v-if="totalGoals > 0" class="text-gray-500 text-sm mt-1">
                            {{ completedGoals }}/{{ totalGoals }} completed
                        </p>
                    </div>
                    <Link
                        :href="route('goals.create')"
                        class="w-10 h-10 rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500 flex items-center justify-center text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-shadow"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                    </Link>
                </div>

                <!-- Overall Progress -->
                <div
                    v-if="totalGoals > 0"
                    class="mb-8 p-4 rounded-2xl bg-gray-900 border border-gray-800"
                >
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-400 text-sm">Overall Progress</span>
                        <span class="text-white font-bold">{{ overallProgress }}%</span>
                    </div>
                    <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500 transition-all duration-500"
                            :style="{ width: `${overallProgress}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="totalGoals === 0" class="text-center py-16">
                    <div
                        class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-900 flex items-center justify-center"
                    >
                        <svg
                            class="w-10 h-10 text-gray-700"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-white mb-2">No goals yet</h2>
                    <p class="text-gray-500 mb-6">Start tracking your progress</p>
                    <Link
                        :href="route('goals.create')"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white font-medium shadow-lg shadow-violet-500/25"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Create Goal
                    </Link>
                </div>

                <!-- Goals by Category -->
                <div v-else class="space-y-8">
                    <section v-for="category in categories" :key="category.value">
                        <div v-if="goals[category.value]?.length">
                            <!-- Category Header -->
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-3 h-3 rounded-full"
                                    :style="{ backgroundColor: category.color }"
                                ></div>
                                <h2 class="text-lg font-semibold text-white">
                                    {{ category.label }}
                                </h2>
                                <span
                                    class="text-xs text-gray-500 bg-gray-800 px-2 py-0.5 rounded-full"
                                >
                                    {{ goals[category.value].length }}
                                </span>
                            </div>

                            <!-- Goals List -->
                            <div class="space-y-3">
                                <div
                                    v-for="goal in goals[category.value]"
                                    :key="goal.id"
                                    class="flex items-center gap-2"
                                >
                                    <Link
                                        :href="route('goals.show', goal.id)"
                                        class="flex-1 p-4 rounded-xl bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors group"
                                    >
                                        <div class="flex items-center justify-between mb-3">
                                            <h3
                                                class="font-medium text-white group-hover:text-violet-400 transition-colors line-clamp-1"
                                            >
                                                {{ goal.title }}
                                            </h3>
                                            <span
                                                v-if="getProgress(goal) >= 100"
                                                class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400"
                                            >
                                                Done
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden"
                                            >
                                                <div
                                                    class="h-full rounded-full transition-all duration-300"
                                                    :class="
                                                        getProgress(goal) >= 100
                                                            ? 'bg-emerald-500'
                                                            : ''
                                                    "
                                                    :style="{
                                                        width: `${getProgress(goal)}%`,
                                                        backgroundColor:
                                                            getProgress(goal) >= 100
                                                                ? undefined
                                                                : category.color,
                                                    }"
                                                ></div>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 tabular-nums min-w-[60px] text-right"
                                            >
                                                {{ getProgressText(goal) }}
                                            </span>
                                        </div>
                                    </Link>
                                    <!-- Quick Log Button -->
                                    <button
                                        v-if="canQuickLog(goal)"
                                        @click="quickLog(goal, $event)"
                                        :disabled="processing === goal.id"
                                        class="w-12 h-12 rounded-xl flex items-center justify-center transition-all shrink-0"
                                        :class="
                                            goal.type === 'yes_no' && goal.is_completed
                                                ? 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30'
                                                : 'bg-gray-800 text-gray-400 hover:bg-violet-500/20 hover:text-violet-400'
                                        "
                                    >
                                        <svg
                                            v-if="processing === goal.id"
                                            class="w-5 h-5 animate-spin"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        <svg
                                            v-else-if="
                                                goal.type === 'yes_no' && goal.is_completed
                                            "
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Archived Link -->
                <div v-if="archivedCount > 0" class="mt-8 text-center">
                    <Link
                        :href="route('goals.archived')"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-400 transition-colors"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                            />
                        </svg>
                        {{ archivedCount }} archived
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
