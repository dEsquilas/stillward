<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { availableLocales } from '@/locales';

const { t, locale } = useI18n();
const showMobileMenu = ref(false);
const showUserMenu = ref(false);

const currentLocale = () => availableLocales.find((l) => l.code === locale.value);

const changeLocale = (code) => {
    locale.value = code;
    localStorage.setItem('locale', code);
};

const closeMenus = (e) => {
    if (!e.target.closest('.user-menu-container')) {
        showUserMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeMenus);
});

onUnmounted(() => {
    document.removeEventListener('click', closeMenus);
});
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <!-- Fixed Top Navbar -->
        <nav
            class="fixed top-0 left-0 right-0 z-50 h-14 bg-gray-900/95 backdrop-blur-sm border-b border-gray-800"
        >
            <div class="h-full max-w-7xl mx-auto px-4 flex items-center justify-between">
                <!-- Logo -->
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center"
                    >
                        <svg
                            class="w-5 h-5 text-white"
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
                    <span class="text-white font-semibold hidden sm:block">{{
                        $t('app_name')
                    }}</span>
                </Link>

                <!-- Center Nav (Desktop) -->
                <div class="hidden sm:flex items-center gap-1 bg-gray-800/50 rounded-full p-1">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium transition-all"
                        :class="
                            route().current('dashboard')
                                ? 'bg-gray-700 text-white'
                                : 'text-gray-400 hover:text-white'
                        "
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
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                        <span>{{ $t('nav.dashboard') }}</span>
                    </Link>
                    <Link
                        :href="route('goals.index')"
                        class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium transition-all"
                        :class="
                            route().current('goals.*')
                                ? 'bg-gray-700 text-white'
                                : 'text-gray-400 hover:text-white'
                        "
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
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <span>{{ $t('nav.goals') }}</span>
                    </Link>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-2">
                    <!-- Mobile Menu Toggle -->
                    <button
                        @click="showMobileMenu = !showMobileMenu"
                        class="sm:hidden w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-white transition-colors"
                    >
                        <svg
                            v-if="!showMobileMenu"
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 6h16M4 12h16M4 18h16"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                    <!-- User Avatar with Dropdown -->
                    <div class="relative user-menu-container">
                        <button
                            @click="showUserMenu = !showUserMenu"
                            class="w-9 h-9 rounded-full overflow-hidden ring-2 transition-all"
                            :class="
                                showUserMenu
                                    ? 'ring-violet-500'
                                    : 'ring-gray-700 hover:ring-violet-500'
                            "
                        >
                            <img
                                v-if="$page.props.auth.user.avatar"
                                :src="$page.props.auth.user.avatar"
                                :alt="$page.props.auth.user.name"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-white font-semibold text-sm"
                            >
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <Transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="opacity-0 scale-95"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <div
                                v-if="showUserMenu"
                                class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-xl border border-gray-700 shadow-xl overflow-hidden"
                            >
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-700">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $page.props.auth.user.name }}
                                    </p>
                                    <p class="text-xs text-gray-400 truncate">
                                        {{ $page.props.auth.user.email }}
                                    </p>
                                </div>

                                <!-- Menu Items -->
                                <div class="py-1">
                                    <Link
                                        :href="route('profile.edit')"
                                        @click="showUserMenu = false"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 transition-colors"
                                    >
                                        <svg
                                            class="w-4 h-4 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        {{ $t('nav.profile') }}
                                    </Link>
                                </div>

                                <!-- Language Switcher -->
                                <div class="py-1 border-t border-gray-700">
                                    <div class="px-4 py-2">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">
                                            Language
                                        </p>
                                        <div class="flex gap-2">
                                            <button
                                                v-for="lang in availableLocales"
                                                :key="lang.code"
                                                @click="changeLocale(lang.code)"
                                                class="flex-1 py-2 rounded-lg text-center text-sm transition-colors"
                                                :class="
                                                    locale === lang.code
                                                        ? 'bg-violet-500/20 text-violet-400'
                                                        : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
                                                "
                                            >
                                                {{ lang.flag }} {{ lang.code.toUpperCase() }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logout -->
                                <div class="py-1 border-t border-gray-700">
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        @click="showUserMenu = false"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 transition-colors"
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
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                            />
                                        </svg>
                                        {{ $t('nav.logout') }}
                                    </Link>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div
            v-if="showMobileMenu"
            class="fixed inset-0 z-40 pt-14 bg-gray-950/98 backdrop-blur-sm sm:hidden"
        >
            <div class="p-4 space-y-2">
                <Link
                    :href="route('dashboard')"
                    @click="showMobileMenu = false"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
                    :class="
                        route().current('dashboard')
                            ? 'bg-gray-800 text-white'
                            : 'text-gray-400 hover:bg-gray-900 hover:text-white'
                    "
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
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        />
                    </svg>
                    <span class="font-medium">{{ $t('nav.dashboard') }}</span>
                </Link>
                <Link
                    :href="route('goals.index')"
                    @click="showMobileMenu = false"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
                    :class="
                        route().current('goals.*')
                            ? 'bg-gray-800 text-white'
                            : 'text-gray-400 hover:bg-gray-900 hover:text-white'
                    "
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
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <span class="font-medium">{{ $t('nav.goals') }}</span>
                </Link>

                <!-- Language in mobile -->
                <div class="px-4 py-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Language</p>
                    <div class="flex gap-2">
                        <button
                            v-for="lang in availableLocales"
                            :key="lang.code"
                            @click="changeLocale(lang.code)"
                            class="flex-1 py-2 rounded-lg text-center text-sm transition-colors"
                            :class="
                                locale === lang.code
                                    ? 'bg-violet-500/20 text-violet-400'
                                    : 'bg-gray-800 text-gray-300 hover:bg-gray-700'
                            "
                        >
                            {{ lang.flag }} {{ lang.code.toUpperCase() }}
                        </button>
                    </div>
                </div>

                <Link
                    :href="route('profile.edit')"
                    @click="showMobileMenu = false"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition-all"
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
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                    <span class="font-medium">{{ $t('nav.profile') }}</span>
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    @click="showMobileMenu = false"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 transition-all"
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
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                    <span class="font-medium">{{ $t('nav.logout') }}</span>
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <main class="pt-14">
            <slot />
        </main>
    </div>
</template>
