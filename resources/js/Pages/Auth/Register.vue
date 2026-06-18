<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registar Conta" />

    <div class="auth-wrapper flex-col">
        <div class="banner-dev w-full max-w-md mx-4">
            ⚠️ Registro aberto para a versão de testes do MVP
        </div>

        <div class="auth-card">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Criar Nova Conta</h2>
                <p class="text-sm text-gray-500 mt-1">Comece a gerir as suas finanças hoje</p>
            </div>

            <form @submit.prevent="submit">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input id="name" type="text" class="form-input" v-model="form.name" required autofocus autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="form-group">
                    <label for="email">E-mail Corporativo ou Pessoal</label>
                    <input id="email" type="email" class="form-input" v-model="form.email" required autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="form-group">
                    <label for="password">Definir Senha</label>
                    <input id="password" type="password" class="form-input" v-model="form.password" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="form-group mb-6">
                    <label for="password_confirmation">Confirmar Senha</label>
                    <input id="password_confirmation" type="password" class="form-input" v-model="form.password_confirmation" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <button class="btn-auth-submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Finalizar Cadastro
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-6">
                Já tem uma conta registada? 
                <Link :href="route('login')" class="text-blue-600 font-semibold hover:underline">Iniciar Sessão</Link>
            </p>
        </div>
    </div>
</template>

<style>
@import '../../../css/custom.css';
</style>