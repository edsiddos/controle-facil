<script setup>
import { useForm, router } from '@inertiajs/vue3';
import Form from './Form.vue';

/**
 * Propriedades enviadas pela rota do Laravel (Controller)
 */
defineProps({
    accountCards: Array,
    categories: Array
});

/**
 * Inicialização do estado reativo do formulário com valores padrão
 */
const form = useForm({
    description: '',
    amount: '',
    transaction_date: '',
    account_card_id: '',
    category_id: '',
    status: 'A PAGAR',
    installment_number: null,
    is_recurring: false
});

/**
 * Envia a requisição POST para persistência no banco
 */
const submit = () => {
    form.post(route('transactions.store'));
};

/**
 * Redireciona de volta para o Index
 */
const cancel = () => {
    router.get(route('transactions.index'));
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Nova Transação Financeira</h2>

        <form @submit.prevent="submit">
            <Form :form="form" :account-cards="accountCards" :categories="categories" />

            <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                <button type="button" @click="cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none">
                    Cancelar
                </button>
                <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none disabled:opacity-50">
                    {{ form.processing ? 'Salvando...' : 'Salvar Transação' }}
                </button>
            </div>
        </form>
    </div>
</template>