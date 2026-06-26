<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WebTable from '@/Components/WebTable.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const tableHeaders = [['Nome', 'Tipo de Conta', 'Saldo Atual', 'Limite Disponível']];
const tableBodyFields = ['name', 'account_type.name', 'balance', 'available_limit'];

// Referência para controlar o componente WebTable de fora
const tableRef = ref(null);

// Estados do modal de exclusão
const showDeleteUser = ref(false);
const deleteAccountUser = ref(null);

// Função engatada no evento @delete emitido pela tabela
const openDeleteModal = (id) => {
    deleteAccountUser.value = id;
    showDeleteUser.value = true;
};

const deleteAccount = () => {
    router.delete(route('accounts.destroy', deleteAccountUser.value), {
        onSuccess: () => {
            closeModal();
            // Recarrega os dados internamente no WebTable sem recarregar a página
            tableRef.value.searchData();
        }
    });
};

const closeModal = () => {
    showDeleteUser.value = false;
};
</script>

<template>

    <Head title="Contas e Cartões" />

    <AuthenticatedLayout>
        <template #header-title>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gerenciamento de Contas e Cartões</h2>
            </div>
        </template>

        <template #toolbar>
            <Link :href="route('accounts.create')"
                class="bg-blue-600 text-white px-4 py-2 rounded shadow text-sm font-medium">
                💳 Novo Cartão / Conta
            </Link>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow sm:rounded-lg">

                    <WebTable ref="tableRef" :headers="tableHeaders" :body="tableBodyFields" link="accounts.web-table"
                        :actions="true" edit-route="accounts.edit" @delete="openDeleteModal" />

                </div>
            </div>
        </div>

        <Modal :show="showDeleteUser" @close="closeModal" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tem certeza que deseja excluir esta conta?</h2>
                <p class="mt-1 text-sm text-gray-600">Esta ação não poderá ser desfeita.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteAccount">Sim, Excluir</DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>