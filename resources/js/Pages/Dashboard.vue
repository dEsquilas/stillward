<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Welcome -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-white">
                        Welcome back, {{ $page.props.auth.user.name.split(' ')[0] }}
                    </h1>
                    <p class="text-gray-500 mt-1">Track your progress</p>
                </div>

                <!-- Quick Actions -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <Link
                        :href="route('goals.index')"
                        class="p-6 rounded-2xl bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors group"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-white"
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
                        <h2
                            class="text-lg font-semibold text-white group-hover:text-violet-400 transition-colors"
                        >
                            Goals
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">View and manage your goals</p>
                    </Link>

                    <Link
                        :href="route('goals.create')"
                        class="p-6 rounded-2xl bg-gray-900 border border-gray-800 hover:border-gray-700 transition-colors group"
                    >
                        <div
                            class="w-12 h-12 rounded-xl bg-gray-800 flex items-center justify-center mb-4 group-hover:bg-violet-500/20 transition-colors"
                        >
                            <svg
                                class="w-6 h-6 text-gray-400 group-hover:text-violet-400 transition-colors"
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
                        <h2
                            class="text-lg font-semibold text-white group-hover:text-violet-400 transition-colors"
                        >
                            New Goal
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Create a new goal to track</p>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
