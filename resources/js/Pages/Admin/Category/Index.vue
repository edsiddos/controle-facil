<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WebTable from '@/Components/WebTable.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const tableHeaders = [['Nome', 'Tipo de Movimentação', 'Ícone']];
const tableBodyFields = ['name', 'type', 'icon'];

const tableRef = ref(null);
const showDeleteModalStatus = ref(false);
const deleteCategoryId = ref(null);

const openDeleteModal = (id) => {
    deleteCategoryId.value = id;
    showDeleteModalStatus.value = true;
};

const deleteCategory = () => {
    router.delete(route('categories.destroy', deleteCategoryId.value), {
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

    <Head title="Categorias" />

    <AuthenticatedLayout>
        <template #header-title>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categorias</h2>
            </div>
        </template>

        <template #toolbar>
            <Link :href="route('categories.create')"
                class="bg-blue-600 text-white px-4 py-2 rounded shadow text-sm font-medium">
                🗂️ Nova Categoria
            </Link>
        </template>

        <div class="py-12">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow sm:rounded-lg">

                    <WebTable ref="tableRef" :headers="tableHeaders" :body="tableBodyFields" link="categories.web-table"
                        :actions="true" edit-route="categories.edit" @delete="openDeleteModal" />

                </div>
            </div>
        </div>

        <Modal :show="showDeleteModalStatus" @close="closeModal" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tem certeza que deseja excluir esta categoria?</h2>
                <p class="mt-1 text-sm text-gray-600">Esta ação não poderá ser desfeita e categorias globais não podem
                    ser
                    deletadas.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteCategory">Sim, Excluir</DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>