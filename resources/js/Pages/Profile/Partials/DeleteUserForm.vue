<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-white">Delete Account</h2>
            <p class="mt-1 text-sm text-gray-400">
                Once your account is deleted, all of its resources and data will be permanently
                deleted. Before deleting your account, please download any data or information that
                you wish to retain.
            </p>
        </header>

        <button
            @click="confirmUserDeletion"
            class="mt-6 px-6 py-3 rounded-xl bg-red-500/20 text-red-400 font-medium hover:bg-red-500/30 transition-colors"
        >
            Delete Account
        </button>

        <!-- Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="confirmingUserDeletion"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div class="fixed inset-0 bg-black/80" @click="closeModal"></div>
                    <div
                        class="relative w-full max-w-md p-6 rounded-2xl bg-gray-900 border border-gray-800"
                    >
                        <h2 class="text-lg font-medium text-white">
                            Are you sure you want to delete your account?
                        </h2>

                        <p class="mt-2 text-sm text-gray-400">
                            Once your account is deleted, all of its resources and data will be
                            permanently deleted. Please enter your password to confirm you would
                            like to permanently delete your account.
                        </p>

                        <div class="mt-6">
                            <input
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                placeholder="Password"
                                @keyup.enter="deleteUser"
                                class="w-full px-4 py-3 rounded-xl bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                            />
                            <p v-if="form.errors.password" class="mt-2 text-sm text-red-400">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                @click="closeModal"
                                class="px-6 py-3 rounded-xl bg-gray-800 text-gray-300 font-medium hover:bg-gray-700 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="deleteUser"
                                :disabled="form.processing"
                                class="px-6 py-3 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 transition-colors disabled:opacity-50"
                            >
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>
