<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WebTable from '@/Components/WebTable.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const tableHeaders = [['Contas e Cartão', 'Descrição', 'Valor Total', 'Qtd. Parcelas', 'DT. Compra']];
const tableBodyFields = ['account_card_id', 'description', 'total_amount', 'total_installments', 'purchase_date'];

const tableRef = ref(null);
const showDeleteModalStatus = ref(false);
const deleteCategoryId = ref(null);

const openDeleteModal = (id) => {
    deleteCategoryId.value = id;
    showDeleteModalStatus.value = true;
};

const deleteCategory = () => {
    router.delete(route('installment-purchases.destroy', deleteCategoryId.value), {
        onSuccess: () => {
            closeModal();
            tableRef.value.searchData();
        }
    });
};

const closeModal = () => {
    showDeleteModalStatus.value = false;
};
</script>

<template>

    <Head title="Compras Parceladas" />

    <AuthenticatedLayout>
        <template #header-title>
            <div class="flex justify-between items-center w-full">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">🛍️ Compras Parceladas</h2>
            </div>
        </template>

        <template #toolbar>
            <Link :href="route('installment-purchases.create')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dynamic-button">
                🛍️ Nova Compra
            </Link>
        </template>

        <div class="py-12">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow sm:rounded-lg">

                    <WebTable ref="tableRef" :headers="tableHeaders" :body="tableBodyFields"
                        link="installment-purchases.web-table" :actions="true" edit-route="installment-purchases.edit"
                        @delete="openDeleteModal" />
                </div>
            </div>
        </div>

        <Modal :show="showDeleteModalStatus" @close="closeModal" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tem certeza que deseja excluir esta compra parcelada?</h2>
                <p class="mt-1 text-sm text-gray-600">Esta ação não poderá ser desfeita.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteCategory">Sim, Excluir</DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>