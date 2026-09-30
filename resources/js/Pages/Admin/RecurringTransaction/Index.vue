<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WebTable from '@/Components/WebTable.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const tableRef = ref(null);
const showCancelModal = ref(false);
const recurringIdToCancel = ref(null);

/**
 * Títulos das colunas renderizadas na tabela de transações recorrentes
 */
const tableHeaders = [['Descrição', 'Conta/Cartão', 'Categoria', 'Valor', 'Data Início', 'Parcelas', 'Status']];

/**
 * Propriedades dos dados exibidos nas colunas
 */
const tableBodyFields = [
    'description',
    'account_card.name',
    'category.name',
    'amount',
    'start_date',
    'installment_number',
    'status'
];

/**
 * Abre o modal de confirmação para cancelamento do plano/assinatura
 * @param {number} id ID da assinatura/plano recorrente
 */
const openCancelModal = (id) => {
    recurringIdToCancel.value = id;
    showCancelModal.value = true;
};

/**
 * Confirma o cancelamento da assinatura e atualiza a WebTable
 */
const confirmCancel = () => {
    router.post(route('recurring-transactions.cancel', recurringIdToCancel.value), {}, {
        onSuccess: () => {
            closeModal();
            if (tableRef.value) {
                tableRef.value.searchData();
            }
        }
    });
};

const closeModal = () => {
    showCancelModal.value = false;
    recurringIdToCancel.value = null;
};
</script>

<template>
    <Head title="Transações Recorrentes" />

    <AuthenticatedLayout>
        <template #header-title>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transações Recorrentes & Assinaturas</h2>
            </div>
        </template>

        <div class="p-6">
            <div class="bg-white shadow rounded-lg p-4">
                <WebTable
                    ref="tableRef"
                    :headers="tableHeaders"
                    :body="tableBodyFields"
                    link="recurring-transactions.web-table"
                    :actions="true"
                >
                    <template #actions="{ row }">
                        <button
                            v-if="row.status === 'ATIVO'"
                            @click="openCancelModal(row.id)"
                            class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-md text-xs font-semibold transition"
                        >
                            🚫 Cancelar Plano
                        </button>
                        <span v-else class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-500 rounded-md">
                            Cancelado
                        </span>
                    </template>
                </WebTable>
            </div>

            <Modal :show="showCancelModal" @close="closeModal" max-width="md">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Tem certeza que deseja cancelar esta assinatura/plano recorrente?
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Ao informar o cancelamento, o status da assinatura será alterado para <strong>CANCELADO</strong> e todas as transações futuras serão removidas do sistema.
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Voltar</SecondaryButton>
                        <DangerButton @click="confirmCancel">Confirmar Cancelamento</DangerButton>
                    </div>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>
