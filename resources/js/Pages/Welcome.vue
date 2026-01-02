<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Stillward" />

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex flex-col items-center justify-center px-6">
        <div class="text-center">
            <img src="/logo.svg" alt="Stillward" class="w-24 h-24 mx-auto mb-6" />
            <h1 class="text-5xl font-bold text-white mb-4">
                Stillward
            </h1>
            <p class="text-xl text-gray-400 mb-12 max-w-md">
                Track your new year's resolutions and achieve your goals.
            </p>

            <div v-if="canLogin">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-8 py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition"
                >
                    Go to Dashboard
                </Link>

                <Link
                    v-else
                    :href="route('login')"
                    class="px-8 py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition"
                >
                    Get Started
                </Link>
            </div>
        </div>

        <footer class="absolute bottom-8 text-gray-500 text-sm">
            Start tracking your progress today.
        </footer>
    </div>
</template>
