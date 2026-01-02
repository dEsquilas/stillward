<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/" class="flex flex-col items-center">
                <img src="/logo.svg" alt="Stillward" class="h-16 w-16" />
                <span class="mt-2 text-xl font-bold text-white">Stillward</span>
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-gray-800 px-6 py-4 sm:max-w-md sm:rounded-lg"
            style="box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);"
        >
            <slot />
        </div>
    </div>
</template>
