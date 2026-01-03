<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    goals: Object,
    categories: Array,
});

const restore = (goalId) => {
    router.post(route('goals.restore', goalId));
};
</script>

<template>
    <Head :title="$t('goals.archived')" />

    <AuthenticatedLayout>
        <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-8">
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
                    <h1 class="text-xl font-bold text-white">{{ $t('goals.archived') }}</h1>
                </div>

                <!-- Empty State -->
                <div v-if="Object.keys(goals).length === 0" class="text-center py-16">
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-900 flex items-center justify-center"
                    >
                        <svg
                            class="w-8 h-8 text-gray-700"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                            />
                        </svg>
                    </div>
                    <p class="text-gray-500">{{ $t('goals.no_archived') }}</p>
                </div>

                <!-- Archived Goals -->
                <div v-else class="space-y-6">
                    <section v-for="category in categories" :key="category.value">
                        <div v-if="goals[category.value]?.length">
                            <!-- Category Header -->
                            <div class="flex items-center gap-2 mb-3">
                                <div
                                    class="w-2 h-2 rounded-full"
                                    :style="{ backgroundColor: category.color }"
                                ></div>
                                <span class="text-sm text-gray-500">{{
                                    $t(`categories.${category.value}`)
                                }}</span>
                            </div>

                            <!-- Goals -->
                            <div class="space-y-2">
                                <div
                                    v-for="goal in goals[category.value]"
                                    :key="goal.id"
                                    class="flex items-center justify-between p-4 rounded-xl bg-gray-900 border border-gray-800"
                                >
                                    <span class="text-gray-400">{{ goal.title }}</span>
                                    <button
                                        @click="restore(goal.id)"
                                        class="text-sm text-violet-400 hover:text-violet-300 transition-colors"
                                    >
                                        {{ $t('detail.restore') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
