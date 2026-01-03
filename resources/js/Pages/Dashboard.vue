<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const { t } = useI18n();

const props = defineProps({
    stats: Object,
    categoryStats: Array,
    recentActivity: Array,
    weeklyProgress: Array,
    categories: Array,
});

const chartData = computed(() => ({
    labels: props.weeklyProgress.map((w) => w.week),
    datasets: [
        {
            label: 'Logs',
            data: props.weeklyProgress.map((w) => w.logs),
            backgroundColor: 'rgba(139, 92, 246, 0.5)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 1,
            borderRadius: 4,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
            ticks: {
                color: '#6b7280',
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(55, 65, 81, 0.5)',
            },
            ticks: {
                color: '#6b7280',
                stepSize: 1,
            },
        },
    },
};

const getCategoryColor = (categoryValue) => {
    const cat = props.categories?.find((c) => c.value === categoryValue);
    return cat?.color || '#8b5cf6';
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

const paceStatus = computed(() => {
    const diff = props.stats.avgProgress - props.stats.yearProgress;
    if (diff >= 5)
        return {
            label: t('dashboard.ahead'),
            color: 'text-emerald-400',
            bg: 'bg-emerald-500/20',
        };
    if (diff >= -5)
        return { label: t('dashboard.on_track'), color: 'text-yellow-400', bg: 'bg-yellow-500/20' };
    return { label: t('dashboard.behind'), color: 'text-red-400', bg: 'bg-red-500/20' };
});
</script>

<template>
    <Head :title="$t('nav.dashboard')" />

    <AuthenticatedLayout>
        <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-white">
                        {{
                            $t('welcome_back', { name: $page.props.auth.user.name.split(' ')[0] })
                        }}
                    </h1>
                    <p class="text-gray-500 mt-1">{{ $t('progress_overview') }}</p>
                </div>

                <!-- Year Progress & Pace -->
                <div class="grid gap-4 sm:grid-cols-2 mb-6">
                    <!-- Year Progress -->
                    <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-gray-400">{{
                                $t('dashboard.year_progress')
                            }}</span>
                            <span class="text-lg font-bold text-white"
                                >{{ stats.yearProgress }}%</span
                            >
                        </div>
                        <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                            <div
                                class="h-full rounded-full bg-gray-600 transition-all duration-500"
                                :style="{ width: `${stats.yearProgress}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Your Progress -->
                    <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-gray-400">{{
                                $t('dashboard.your_progress')
                            }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-white"
                                    >{{ stats.avgProgress }}%</span
                                >
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full"
                                    :class="[paceStatus.color, paceStatus.bg]"
                                >
                                    {{ paceStatus.label }}
                                </span>
                            </div>
                        </div>
                        <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500 transition-all duration-500"
                                :style="{ width: `${stats.avgProgress}%` }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 text-center">
                        <div class="text-3xl font-bold text-white mb-1">{{ stats.totalGoals }}</div>
                        <div class="text-sm text-gray-500">{{ $t('dashboard.active_goals') }}</div>
                    </div>
                    <div class="p-5 rounded-2xl bg-gray-900 border border-gray-800 text-center">
                        <div class="text-3xl font-bold text-emerald-400 mb-1">
                            {{ stats.completedGoals }}
                        </div>
                        <div class="text-sm text-gray-500">{{ $t('dashboard.completed') }}</div>
                    </div>
                </div>

                <!-- Category Stats -->
                <div v-if="categoryStats.length > 0" class="mb-6">
                    <h2 class="text-sm font-medium text-gray-500 mb-3">
                        {{ $t('dashboard.by_category') }}
                    </h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <Link
                            v-for="cat in categoryStats"
                            :key="cat.value"
                            :href="route('goals.index')"
                            class="p-4 rounded-xl bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors"
                        >
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-3 h-3 rounded-full"
                                    :style="{ backgroundColor: cat.color }"
                                ></div>
                                <span class="font-medium text-white">{{
                                    $t(`categories.${cat.value}`)
                                }}</span>
                                <span class="text-xs text-gray-500 ml-auto"
                                    >{{ cat.completed }}/{{ cat.count }}</span
                                >
                            </div>
                            <div class="h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-300"
                                    :style="{
                                        width: `${cat.progress}%`,
                                        backgroundColor: cat.color,
                                    }"
                                ></div>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-sm text-gray-400">{{ cat.progress }}%</span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Activity Chart -->
                <div v-if="weeklyProgress.length > 0" class="mb-6">
                    <h2 class="text-sm font-medium text-gray-500 mb-3">
                        {{ $t('dashboard.activity_weeks') }}
                    </h2>
                    <div class="p-4 rounded-2xl bg-gray-900 border border-gray-800">
                        <div class="h-48">
                            <Bar :data="chartData" :options="chartOptions" />
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div v-if="recentActivity.length > 0" class="mb-6">
                    <h2 class="text-sm font-medium text-gray-500 mb-3">
                        {{ $t('dashboard.recent_activity') }}
                    </h2>
                    <div class="space-y-2">
                        <Link
                            v-for="entry in recentActivity"
                            :key="entry.id"
                            :href="entry.goal ? route('goals.show', entry.goal.id) : '#'"
                            class="flex items-center gap-3 p-3 rounded-xl bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors"
                        >
                            <div
                                class="w-2 h-2 rounded-full shrink-0"
                                :style="{
                                    backgroundColor: entry.goal
                                        ? getCategoryColor(entry.goal.category)
                                        : '#6b7280',
                                }"
                            ></div>
                            <div class="flex-1 min-w-0">
                                <span class="text-gray-300 truncate block">{{
                                    entry.goal?.title || 'Unknown goal'
                                }}</span>
                            </div>
                            <span class="text-sm text-gray-400 shrink-0">
                                {{ parseFloat(entry.value) >= 0 ? '+' : '' }}{{ entry.value }}
                            </span>
                            <span class="text-xs text-gray-600 shrink-0">{{
                                formatDate(entry.created_at)
                            }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="stats.totalGoals === 0"
                    class="text-center py-16 rounded-2xl bg-gray-900 border border-gray-800"
                >
                    <div
                        class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-800 flex items-center justify-center"
                    >
                        <svg
                            class="w-10 h-10 text-gray-600"
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
                    <h2 class="text-xl font-semibold text-white mb-2">
                        {{ $t('dashboard.no_goals_yet') }}
                    </h2>
                    <p class="text-gray-500 mb-6">{{ $t('dashboard.create_first_goal') }}</p>
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
                        {{ $t('goals.create_goal') }}
                    </Link>
                </div>

                <!-- Quick Actions -->
                <div v-if="stats.totalGoals > 0" class="grid gap-4 sm:grid-cols-2">
                    <Link
                        :href="route('goals.index')"
                        class="p-4 rounded-xl bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors flex items-center gap-4"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-violet-500/20 flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-violet-400"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-white">{{ $t('dashboard.view_goals') }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $t('dashboard.manage_goals') }}
                            </div>
                        </div>
                    </Link>

                    <Link
                        :href="route('goals.create')"
                        class="p-4 rounded-xl bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors flex items-center gap-4"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-fuchsia-500/20 flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-fuchsia-400"
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
                        </div>
                        <div>
                            <div class="font-medium text-white">{{ $t('dashboard.new_goal') }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $t('dashboard.create_new_goal') }}
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
