<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="dashboard-layout">
        <aside class="dashboard-sidebar flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-8 px-2">
                    <span class="text-2xl">📊</span>
                    <span class="font-bold text-xl text-gray-900">ControleFácil</span>
                </div>

                <nav class="space-y-1">
                    <Link :href="route('dashboard')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-600 bg-blue-50 border border-blue-100">
                        <span>🏠</span> Visão Geral
                    </Link>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">
                        <span>💳</span> Contas & Cartões
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">
                        <span>📉</span> Despesas Fixas
                    </a>
                </nav>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <div class="px-2 mb-3">
                    <p class="text-sm font-semibold text-gray-800">{{ $page.props.auth.user.name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition">
                    <span>🚪</span> Sair do Sistema
                </Link>
            </div>
        </aside>

        <main class="flex-1 p-8 bg-gray-50 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <slot name="header-title" />
                </div>
                <span class="bg-amber-100 text-amber-800 border border-amber-200 px-3 py-1 rounded-full text-xs font-semibold">
                    MVP v1.0
                </span>
            </header>

            <slot />
        </main>
    </div>
</template>

<style>
@import '../../css/custom.css';
</style>