<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="Bem-vindo ao ControleFácil" />
    
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <div class="banner-dev">
            ⚠️ Modo de Testes Ativo: O sistema ControleFácil encontra-se em fase de desenvolvimento.
        </div>

        <header class="w-full max-w-7xl mx-auto px-6 py-4 flex justify-between items-center bg-white rounded-xl shadow-sm mt-2 border border-gray-200">
            <div class="text-2xl font-bold text-blue-600 flex items-center gap-2">
                📊 <span>ControleFácil</span>
            </div>
            <nav v-if="canLogin" class="flex gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="px-4 py-2 font-medium text-gray-700 hover:text-blue-600 transition">
                    Ir para o Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="px-4 py-2 font-medium text-gray-700 hover:text-blue-600 transition">
                        Entrar
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" class="px-4 py-2 font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm transition">
                        Criar Conta
                    </Link>
                </template>
            </nav>
        </header>

        <main class="flex-1 flex flex-col justify-center items-center px-6 py-12 max-w-4xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight mb-6">
                Gerencie os seus gastos pessoais e compras parceladas <span class="text-blue-600">sem complicação</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mb-10">
                Uma plataforma moderna e limpa para organizar as suas faturas, prever parcelamentos futuros e manter as suas finanças no azul com poucos cliques.
            </p>
            
            <div class="flex flex-wrap gap-4 justify-center">
                <Link :href="route('register')" class="px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition transform hover:-translate-y-0.5">
                    Começar Gratuitamente
                </Link>
                <a href="#features" class="px-8 py-4 bg-white text-gray-700 font-semibold rounded-lg shadow-sm border border-gray-300 hover:bg-gray-50 transition">
                    Conhecer Recursos
                </a>
            </div>
        </main>
    </div>
</template>

<style>
@import '../../css/custom.css';
</style>