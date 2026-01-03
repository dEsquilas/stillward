<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    goal: Object,
    categories: Array,
});

const category = computed(
    () => props.categories?.find((c) => c.value === props.goal.category) || {}
);

const progress = computed(() => {
    if (props.goal.type === 'yes_no') return props.goal.is_completed ? 100 : 0;
    if (props.goal.type === 'percentage') return parseFloat(props.goal.current_value) || 0;
    if (props.goal.target_value > 0)
        return Math.min(100, (props.goal.current_value / props.goal.target_value) * 100);
    return 0;
});

const progressText = computed(() => {
    if (props.goal.type === 'yes_no')
        return props.goal.is_completed ? t('detail.completed') : t('goals.not_completed');
    if (props.goal.type === 'percentage') return `${props.goal.current_value}%`;
    if (props.goal.type === 'money')
        return `${props.goal.currency} ${props.goal.current_value} / ${props.goal.target_value}`;
    return `${props.goal.current_value} / ${props.goal.target_value} ${props.goal.unit || ''}`;
});

const isCompleted = computed(() => progress.value >= 100);

// Quick Log
const logForm = useForm({
    value: '',
    note: '',
});

const customValue = ref('');
const percentageValue = ref(parseFloat(props.goal.current_value) || 0);

const logProgress = (value) => {
    logForm.value = value;
    logForm.post(route('goals.log', props.goal.id), {
        preserveScroll: true,
        onSuccess: () => {
            logForm.reset();
            customValue.value = '';
        },
    });
};

const logCustomValue = () => {
    if (customValue.value) {
        logProgress(parseFloat(customValue.value));
    }
};

const toggleComplete = () => {
    logProgress(props.goal.is_completed ? 0 : 1);
};

const updatePercentage = () => {
    logProgress(percentageValue.value);
};

const archive = () => {
    if (confirm(t('detail.confirm_archive'))) {
        router.post(route('goals.archive', props.goal.id));
    }
};

const deleteGoal = () => {
    if (confirm(t('detail.confirm_delete'))) {
        router.delete(route('goals.destroy', props.goal.id));
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return t('time.just_now');
    if (minutes < 60) return t('time.minutes_ago', { count: minutes });
    if (hours < 24) return t('time.hours_ago', { count: hours });
    if (days < 7) return t('time.days_ago', { count: days });
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};
</script>

<template>
    <Head :title="goal.title" />

    <AuthenticatedLayout>
        <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-lg mx-auto">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-6">
                    <Link
                        :href="route('goals.index')"
                        class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-white transition-colors"
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
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </Link>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <div
                                class="w-2 h-2 rounded-full"
                                :style="{ backgroundColor: category.color }"
                            ></div>
                            <span class="text-xs text-gray-500">{{
                                $t(`categories.${category.value}`)
                            }}</span>
                        </div>
                        <h1 class="text-xl font-bold text-white truncate">{{ goal.title }}</h1>
                    </div>
                    <Link
                        :href="route('goals.edit', goal.id)"
                        class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-white transition-colors"
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
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                    </Link>
                </div>

                <!-- Progress Card -->
                <div class="p-6 rounded-2xl bg-gray-900 border border-gray-800 mb-6">
                    <div class="text-center mb-6">
                        <div
                            class="text-5xl font-bold mb-2"
                            :class="isCompleted ? 'text-emerald-400' : 'text-white'"
                        >
                            {{ Math.round(progress) }}%
                        </div>
                        <div class="text-gray-500">{{ progressText }}</div>
                    </div>
                    <div class="h-3 bg-gray-800 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-500"
                            :class="isCompleted ? 'bg-emerald-500' : ''"
                            :style="{
                                width: `${progress}%`,
                                backgroundColor: isCompleted ? undefined : category.color,
                            }"
                        ></div>
                    </div>
                    <div v-if="isCompleted" class="mt-4 text-center">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-sm font-medium"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            {{ $t('detail.goal_completed') }}
                        </span>
                    </div>
                </div>

                <!-- Quick Log -->
                <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800 mb-6">
                    <h2 class="text-sm font-medium text-gray-500 mb-4">
                        {{ $t('detail.log_progress') }}
                    </h2>

                    <!-- Counter Type -->
                    <div v-if="goal.type === 'counter'" class="space-y-3">
                        <div class="flex gap-2">
                            <button
                                @click="logProgress(-1)"
                                :disabled="logForm.processing"
                                class="flex-1 py-3 rounded-xl bg-gray-800 text-white font-medium hover:bg-gray-700 transition-colors disabled:opacity-50"
                            >
                                -1
                            </button>
                            <button
                                @click="logProgress(1)"
                                :disabled="logForm.processing"
                                class="flex-1 py-3 rounded-xl bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white font-medium shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all disabled:opacity-50"
                            >
                                +1
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input
                                v-model="customValue"
                                type="number"
                                :placeholder="$t('detail.custom_value')"
                                class="flex-1 px-4 py-3 rounded-xl bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:border-violet-500 focus:ring-1 focus:ring-violet-500"
                            />
                            <button
                                @click="logCustomValue"
                                :disabled="logForm.processing || !customValue"
                                class="px-6 py-3 rounded-xl bg-gray-800 text-white font-medium hover:bg-gray-700 transition-colors disabled:opacity-50"
                            >
                                {{ $t('detail.add') }}
                            </button>
                        </div>
                    </div>

                    <!-- Yes/No Type -->
                    <div v-else-if="goal.type === 'yes_no'" class="flex justify-center">
                        <button
                            @click="toggleComplete"
                            :disabled="logForm.processing"
                            class="w-full py-4 rounded-xl font-medium transition-all disabled:opacity-50"
                            :class="
                                goal.is_completed
                                    ? 'bg-emerald-500/20 text-emerald-400 border-2 border-emerald-500'
                                    : 'bg-gray-800 text-gray-300 hover:bg-gray-700 border-2 border-transparent'
                            "
                        >
                            <span class="flex items-center justify-center gap-2">
                                <svg
                                    v-if="goal.is_completed"
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
                                {{
                                    goal.is_completed
                                        ? $t('detail.completed')
                                        : $t('detail.mark_complete')
                                }}
                            </span>
                        </button>
                    </div>

                    <!-- Percentage Type -->
                    <div v-else-if="goal.type === 'percentage'" class="space-y-4">
                        <div class="flex items-center gap-4">
                            <input
                                v-model="percentageValue"
                                type="range"
                                min="0"
                                max="100"
                                class="flex-1 h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-violet-500"
                            />
                            <span class="text-white font-medium w-12 text-right"
                                >{{ percentageValue }}%</span
                            >
                        </div>
                        <button
                            @click="updatePercentage"
                            :disabled="logForm.processing"
                            class="w-full py-3 rounded-xl bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white font-medium shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all disabled:opacity-50"
                        >
                            {{ $t('detail.update_progress') }}
                        </button>
                    </div>

                    <!-- Money Type -->
                    <div v-else-if="goal.type === 'money'" class="space-y-3">
                        <div class="flex gap-2">
                            <span
                                class="px-4 py-3 rounded-xl bg-gray-800 text-gray-400 font-medium"
                            >
                                {{ goal.currency }}
                            </span>
                            <input
                                v-model="customValue"
                                type="number"
                                step="0.01"
                                :placeholder="$t('detail.amount')"
                                class="flex-1 px-4 py-3 rounded-xl bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:border-violet-500 focus:ring-1 focus:ring-violet-500"
                            />
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="
                                    customValue &&
                                        logProgress(-Math.abs(parseFloat(customValue)))
                                "
                                :disabled="logForm.processing || !customValue"
                                class="flex-1 py-3 rounded-xl bg-gray-800 text-white font-medium hover:bg-gray-700 transition-colors disabled:opacity-50"
                            >
                                - {{ $t('detail.subtract') }}
                            </button>
                            <button
                                @click="
                                    customValue && logProgress(Math.abs(parseFloat(customValue)))
                                "
                                :disabled="logForm.processing || !customValue"
                                class="flex-1 py-3 rounded-xl bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white font-medium shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all disabled:opacity-50"
                            >
                                + {{ $t('detail.add') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div
                    v-if="goal.description"
                    class="p-4 rounded-xl bg-gray-900 border border-gray-800 mb-6"
                >
                    <p class="text-gray-400 whitespace-pre-wrap">{{ goal.description }}</p>
                </div>

                <!-- Activity -->
                <div v-if="goal.log_entries?.length" class="mb-6">
                    <h2 class="text-sm font-medium text-gray-500 mb-3">
                        {{ $t('dashboard.recent_activity') }}
                    </h2>
                    <div class="space-y-2">
                        <div
                            v-for="entry in goal.log_entries.slice(0, 5)"
                            :key="entry.id"
                            class="flex items-center justify-between p-3 rounded-xl bg-gray-900 border border-gray-800"
                        >
                            <span class="text-gray-300">{{
                                entry.note ||
                                (parseFloat(entry.value) >= 0
                                    ? `+${entry.value}`
                                    : `${entry.value}`)
                            }}</span>
                            <span class="text-xs text-gray-600">{{
                                formatDate(entry.created_at)
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button
                        @click="archive"
                        class="flex-1 py-3 rounded-xl bg-gray-800 text-gray-300 font-medium hover:bg-gray-700 transition-colors flex items-center justify-center gap-2"
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
                        {{ $t('detail.archive') }}
                    </button>
                    <button
                        @click="deleteGoal"
                        class="py-3 px-4 rounded-xl text-red-400 hover:bg-red-500/10 transition-colors"
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
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
