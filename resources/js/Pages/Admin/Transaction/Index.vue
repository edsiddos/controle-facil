<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WebTable from '@/Components/WebTable.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const tableRef = ref(null);
const showDeleteModal = ref(false);
const transactionIdToDelete = ref(null);

/**
 * Títulos das colunas renderizadas na tabela
 */
const tableHeaders = [['Data', 'Descrição', 'Conta/Cartão', 'Categoria', 'Valor', 'Status']];

/**
 * Propriedades internas e aninhadas dos dados que preencherão o corpo
 */
const tableBodyFields = [
    'transaction_date',
    'description',
    'account_card.name',
    'category.name',
    'amount',
    'status'
];

/**
 * Captura o ID emitido pela linha da tabela e exibe o modal
 * @param {Object} row Registro completo da linha clicada
 */
const openDeleteModal = (id) => {
    transactionIdToDelete.value = id;
    showDeleteModal.value = true;
};

/**
 * Envia a requisição de destruição e força a atualização do componente WebTable
 */
const confirmDelete = () => {
    router.delete(route('transactions.destroy', transactionIdToDelete.value), {
        onSuccess: () => {
            closeModal();
            tableRef.value.searchData();
        }
    });
};

const closeModal = () => {
    showDeleteModal.value = false;
};
</script>

<template>

    <Head title="Transações" />

    <AuthenticatedLayout>
        <template #header-title>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transações</h2>
            </div>
        </template>

        <template #toolbar>
            <Link :href="route('transactions.create')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dynamic-button">
                📝 Nova Transação
            </Link>
        </template>

        <div class="p-6">
            <div class="bg-white shadow rounded-lg p-4">
                <WebTable ref="tableRef" :headers="tableHeaders" :body="tableBodyFields" link="transactions.web-table"
                    :actions="true" edit-route="transactions.edit" @delete="openDeleteModal" />
            </div>

            <Modal :show="showDeleteModal" @close="closeModal" max-width="md">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Tem certeza que deseja remover esta transação?.
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">Esta ação não poderá ser desfeita.</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                        <DangerButton @click="confirmDelete">Sim, Excluir</DangerButton>
                    </div>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>
