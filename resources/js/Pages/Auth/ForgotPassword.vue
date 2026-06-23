<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar Palavra-passe" />

    <div class="auth-wrapper flex-col">
        <div class="auth-card">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Recuperar Senha</h2>
                <p class="text-sm text-gray-600 mt-2">
                    Esqueceu-se do seu acesso? Indique o e-mail registado e enviaremos um link seguro para definir uma nova chave.
                </p>
            </div>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 border border-green-200 rounded-lg">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div class="form-group mb-6">
                    <label for="email">O seu e-mail de registo</label>
                    <input id="email" type="email" class="form-input" v-model="form.email" required autofocus autocomplete="username" placeholder="exemplo@email.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <button class="btn-auth-submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Enviar Link de Redefinição
                </button>
            </form>

            <div class="text-center mt-6">
                <a :href="route('login')" class="text-sm text-gray-500 hover:text-blue-600 transition inline-flex items-center gap-1">
                    ← Voltar para a página de Login
                </a>
            </div>
        </div>
    </div>
</template>

<style>
@import '../../../css/custom.css';
</style>