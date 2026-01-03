<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    goal: Object,
    categories: Array,
    types: Array,
});

const form = useForm({
    category: props.goal.category,
    type: props.goal.type,
    title: props.goal.title,
    description: props.goal.description || '',
    target_value: props.goal.target_value || '',
    initial_value: props.goal.initial_value || '',
    unit: props.goal.unit || '',
    increment: props.goal.increment || 1,
    currency: props.goal.currency || 'EUR',
});

const submit = () => {
    form.put(route('goals.update', props.goal.id));
};

const typeIcons = {
    counter: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
    yes_no: 'M5 13l4 4L19 7',
    percentage: 'M16 8v8m-4-5v5m-4-2v2',
    money: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1',
    number: 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14',
};
</script>

<template>
    <Head :title="$t('goals.edit_goal')" />

    <AuthenticatedLayout>
        <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-lg mx-auto">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-8">
                    <Link
                        :href="route('goals.show', goal.id)"
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
                    <h1 class="text-xl font-bold text-white">{{ $t('goals.edit_goal') }}</h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-3">{{
                            $t('form.category')
                        }}</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                v-for="cat in categories"
                                :key="cat.value"
                                type="button"
                                @click="form.category = cat.value"
                                class="p-3 rounded-xl border-2 text-left transition-all"
                                :class="
                                    form.category === cat.value
                                        ? 'border-violet-500 bg-violet-500/10'
                                        : 'border-gray-800 bg-gray-900 hover:border-gray-700'
                                "
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-3 h-3 rounded-full"
                                        :style="{ backgroundColor: cat.color }"
                                    ></div>
                                    <span class="text-sm font-medium text-white">{{
                                        $t(`categories.${cat.value}`)
                                    }}</span>
                                </div>
                            </button>
                        </div>
                        <p v-if="form.errors.category" class="mt-2 text-sm text-red-400">
                            {{ form.errors.category }}
                        </p>
                    </div>

                    <!-- Type (read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-3">{{
                            $t('form.type')
                        }}</label>
                        <div class="p-3 rounded-xl bg-gray-900 border border-gray-800">
                            <div class="flex items-center gap-2">
                                <svg
                                    class="w-4 h-4 text-gray-500"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        :d="typeIcons[form.type]"
                                    />
                                </svg>
                                <span class="text-sm text-gray-400">{{
                                    $t(`types.${form.type}`)
                                }}</span>
                                <span class="text-xs text-gray-600 ml-auto">{{
                                    $t('form.cannot_change')
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-400 mb-2">{{
                            $t('form.title')
                        }}</label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                        />
                        <p v-if="form.errors.title" class="mt-2 text-sm text-red-400">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-400 mb-2"
                        >
                            {{ $t('form.description') }}
                            <span class="text-gray-600">({{ $t('form.optional') }})</span>
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors resize-none"
                        ></textarea>
                    </div>

                    <!-- Counter type fields -->
                    <div v-if="form.type === 'counter'" class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label
                                    for="initial"
                                    class="block text-sm font-medium text-gray-400 mb-2"
                                    >{{ $t('form.initial') }}</label
                                >
                                <input
                                    id="initial"
                                    v-model="form.initial_value"
                                    type="number"
                                    step="any"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                                />
                            </div>
                            <div>
                                <label
                                    for="target"
                                    class="block text-sm font-medium text-gray-400 mb-2"
                                    >{{ $t('form.target') }}</label
                                >
                                <input
                                    id="target"
                                    v-model="form.target_value"
                                    type="number"
                                    step="any"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                                />
                            </div>
                            <div>
                                <label
                                    for="increment"
                                    class="block text-sm font-medium text-gray-400 mb-2"
                                    >{{ $t('form.increment') }}</label
                                >
                                <input
                                    id="increment"
                                    v-model="form.increment"
                                    type="number"
                                    step="any"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                                />
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ $t('form.increment_hint') }}
                        </p>
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ $t('form.unit') }}
                                <span class="text-gray-600">({{ $t('form.optional') }})</span>
                            </label>
                            <input
                                id="unit"
                                v-model="form.unit"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                            />
                        </div>
                    </div>

                    <!-- Number type fields -->
                    <div v-if="form.type === 'number'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    for="initial"
                                    class="block text-sm font-medium text-gray-400 mb-2"
                                    >{{ $t('form.initial') }}</label
                                >
                                <input
                                    id="initial"
                                    v-model="form.initial_value"
                                    type="number"
                                    step="any"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                                />
                            </div>
                            <div>
                                <label
                                    for="target"
                                    class="block text-sm font-medium text-gray-400 mb-2"
                                    >{{ $t('form.target') }}</label
                                >
                                <input
                                    id="target"
                                    v-model="form.target_value"
                                    type="number"
                                    step="any"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-400 mb-2">
                                {{ $t('form.unit') }}
                                <span class="text-gray-600">({{ $t('form.optional') }})</span>
                            </label>
                            <input
                                id="unit"
                                v-model="form.unit"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                            />
                        </div>
                    </div>

                    <!-- Money type fields -->
                    <div v-if="form.type === 'money'" class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="target" class="block text-sm font-medium text-gray-400 mb-2"
                                >{{ $t('form.target') }}</label
                            >
                            <input
                                id="target"
                                v-model="form.target_value"
                                type="number"
                                min="0"
                                step="any"
                                class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white placeholder-gray-600 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                            />
                        </div>
                        <div>
                            <label
                                for="currency"
                                class="block text-sm font-medium text-gray-400 mb-2"
                                >{{ $t('form.currency') }}</label
                            >
                            <select
                                id="currency"
                                v-model="form.currency"
                                class="w-full px-4 py-3 rounded-xl bg-gray-900 border border-gray-800 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition-colors"
                            >
                                <option value="EUR">EUR</option>
                                <option value="USD">USD</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 flex gap-3">
                        <Link
                            :href="route('goals.show', goal.id)"
                            class="flex-1 py-3 rounded-xl bg-gray-800 text-gray-300 font-medium text-center hover:bg-gray-700 transition-colors"
                        >
                            {{ $t('form.cancel') }}
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 py-3 rounded-xl bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white font-medium shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">{{ $t('form.saving') }}</span>
                            <span v-else>{{ $t('form.save') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
