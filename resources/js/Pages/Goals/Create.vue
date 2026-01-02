<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    categories: Array,
    types: Array,
});

const form = useForm({
    category: '',
    type: '',
    title: '',
    description: '',
    target_value: '',
    unit: '',
    currency: 'EUR',
});

const submit = () => {
    form.post(route('goals.store'));
};
</script>

<template>
    <Head title="Create Goal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('goals.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create Goal
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
                        <InputLabel value="Type" />
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <button
                                v-for="t in types"
                                :key="t.value"
                                type="button"
                                @click="form.type = t.value"
                                class="p-3 rounded-lg border-2 text-left transition"
                                :class="form.type === t.value
                                    ? 'border-gray-800 bg-gray-50'
                                    : 'border-gray-200 hover:border-gray-300'"
                            >
                                <span class="font-medium text-gray-900">{{ t.label }}</span>
                                <p class="text-xs text-gray-500 mt-1">
                                    <template v-if="t.value === 'counter'">Track a count towards a target</template>
                                    <template v-else-if="t.value === 'yes_no'">A goal you complete once</template>
                                    <template v-else-if="t.value === 'percentage'">Track progress 0-100%</template>
                                    <template v-else-if="t.value === 'money'">Track a money amount</template>
                                </p>
                            </button>
                        </div>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g., Read 20 books"
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
                            placeholder="Add more details about your goal..."
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
                                placeholder="e.g., books, km, hours"
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
                            <InputError :message="form.errors.currency" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <Link
                            :href="route('goals.index')"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <PrimaryButton :disabled="form.processing">
                            Create Goal
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
