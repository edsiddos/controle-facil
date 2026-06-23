<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Entrar" />

    <div class="auth-wrapper flex-col">
        <div class="banner-dev w-full max-w-md mx-4">
            ⚠️ Ambiente de Desenvolvimento Seguro
        </div>

        <div class="auth-card">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Aceder à sua Conta</h2>
                <p class="text-sm text-gray-500 mt-1">Introduza os seus dados para continuar</p>
            </div>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input id="email" type="email" class="form-input" v-model="form.email" required autofocus autocomplete="username" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="form-group">
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="mb-0">Palavra-passe</label>
                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-blue-600 hover:underline">
                            Esqueceu-se da senha?
                        </Link>
                    </div>
                    <input id="password" type="password" class="form-input" v-model="form.password" required autocomplete="current-password" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="block mb-6">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" v-model="form.remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                        <span class="ms-2 text-sm text-gray-600">Lembrar-me neste dispositivo</span>
                    </label>
                </div>

                <button class="btn-auth-submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Entrar na Plataforma
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-8">
                Não tem uma conta? 
                <Link :href="route('register')" class="text-blue-600 font-semibold hover:underline">Registe-se</Link>
            </p>
        </div>
    </div>
</template>

<style>
@import '../../../css/custom.css';
</style>