<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
    unit: props.goal.unit || '',
    currency: props.goal.currency || 'EUR',
});

const submit = () => {
    form.put(route('goals.update', props.goal.id));
};
</script>

<template>
    <Head title="Edit Goal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('goals.show', goal.id)" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Goal
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6 bg-white p-6 rounded-lg shadow-sm">
                    <div>
                        <InputLabel value="Category" />
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <button
                                v-for="cat in categories"
                                :key="cat.value"
                                type="button"
                                @click="form.category = cat.value"
                                class="p-3 rounded-lg border-2 text-left transition"
                                :class="form.category === cat.value
                                    ? 'border-gray-800 bg-gray-50'
                                    : 'border-gray-200 hover:border-gray-300'"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-3 h-3 rounded-full"
                                        :style="{ backgroundColor: cat.color }"
                                    ></div>
                                    <span class="font-medium text-gray-900">{{ cat.label }}</span>
                                </div>
                            </button>
                        </div>
                        <InputError :message="form.errors.category" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Description (optional)" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div v-if="form.type === 'counter' || form.type === 'money'" class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="target_value" value="Target" />
                            <TextInput
                                id="target_value"
                                v-model="form.target_value"
                                type="number"
                                class="mt-1 block w-full"
                                min="0"
                                step="any"
                            />
                            <InputError :message="form.errors.target_value" class="mt-2" />
                        </div>

                        <div v-if="form.type === 'counter'">
                            <InputLabel for="unit" value="Unit" />
                            <TextInput
                                id="unit"
                                v-model="form.unit"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.unit" class="mt-2" />
                        </div>

                        <div v-if="form.type === 'money'">
                            <InputLabel for="currency" value="Currency" />
                            <select
                                id="currency"
                                v-model="form.currency"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="EUR">EUR</option>
                                <option value="USD">USD</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <Link
                            :href="route('goals.show', goal.id)"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <PrimaryButton :disabled="form.processing">
                            Save Changes
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
