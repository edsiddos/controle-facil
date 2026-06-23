<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    form: Object,
    categories: Array,
    accounts: Array
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Descrição</label>
            <input v-model="form.description" type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required />
            <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Valor Total (R$)</label>
                <input v-model="form.total_amount" type="number" step="0.01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required />
                <div v-if="form.errors.total_amount" class="text-red-500 text-xs mt-1">{{ form.errors.total_amount }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nº de Parcelas</label>
                <input v-model="form.total_installments" type="number" min="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required />
                <div v-if="form.errors.total_installments" class="text-red-500 text-xs mt-1">{{
                    form.errors.total_installments }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Categoria</label>
                <select v-model="form.category_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                    <option value="">Selecione...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <div v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Conta / Cartão</label>
                <select v-model="form.account_card_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                    <option value="">Selecione...</option>
                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
                </select>
                <div v-if="form.errors.account_card_id" class="text-red-500 text-xs mt-1">{{ form.errors.account_card_id
                }}</div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Data da Compra</label>
            <input v-model="form.purchase_date" type="date"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required />
            <div v-if="form.errors.purchase_date" class="text-red-500 text-xs mt-1">{{ form.errors.purchase_date }}
            </div>
        </div>

        <slot name="buttons"></slot>
    </div>
</template>