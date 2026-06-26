<script setup>
import { useForm, router } from '@inertiajs/vue3';
import Form from './Form.vue';

/**
 * Propriedades injetadas pelo Controller correspondente à transação carregada
 */
const props = defineProps({
    transaction: Object,
    accountCards: Array,
    categories: Array
});

/**
 * Mapeamento e inicialização imediata preenchendo com os dados existentes
 */
const form = useForm({
    description: props.transaction.description,
    amount: props.transaction.amount,
    transaction_date: props.transaction.transaction_date ? props.transaction.transaction_date.substring(0, 10) : '',
    account_card_id: props.transaction.account_card_id,
    category_id: props.transaction.category_id,
    status: props.transaction.status,
    installment_number: props.transaction.installment_number,
    is_recurring: !!props.transaction.is_recurring
});

/**
 * Dispara o método PUT/PATCH para atualizar o registro específico
 */
const submit = () => {
    form.put(route('transactions.update', props.transaction.id));
};

const cancel = () => {
    router.get(route('transactions.index'));
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Editar Transação Financeira</h2>

        <form @submit.prevent="submit">
            <Form :form="form" :account-cards="accountCards" :categories="categories" />

            <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                <button type="button" @click="cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                    Cancelar
                </button>
                <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none disabled:opacity-50">
                    {{ form.processing ? 'Atualizando...' : 'Atualizar Transação' }}
                </button>
            </div>
        </form>
    </div>
</template>