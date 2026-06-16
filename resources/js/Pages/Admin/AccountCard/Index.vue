<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import WebTable from '@/Components/WebTable.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showDeleteUser = ref(false);
const deleteAccountUser = ref(null);

const props = defineProps({
    accounts: Array,
    accountTypes: Array,
    typeCreditCard: Number
});

const deleteAccount = () => {
    router.delete(route('accounts.destroy', deleteAccountUser.value), {
        onSuccess: () => closeModal()
    });
};

const closeModal = () => {
    showDeleteUser.value = false;
};
</script>

<template>

    <Head title="Contas e Cartões" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gerenciamento de Contas e Cartões</h2>
                <Link :href="route('accounts.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 text-sm font-medium">
                    + Novo Cartão / Conta
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Minhas Carteiras</h3>

                    <div class="overflow-x-auto">

                        <WebTable :-headers="[['Nome', 'Saldo', 'Limite']]"
                            :-body="['name', 'balance', 'available_limit']" :-route="'accounts.web-table'">
                        </WebTable>

                        <!--
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Limite
                                        Disp.
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="account in accounts" :key="account.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ account.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                        account.account_type?.name
                                    }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ account.balance
                                    }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ account.available_limit ? 'R$ ' + account.available_limit : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-3">
                                        <Link :href="route('accounts.edit', account.id)"
                                            class="text-blue-600 hover:text-blue-900">
                                            Editar
                                        </Link>
                                        <button @click="deleteAccountUser = account.id; showDeleteUser = true;"
                                            class="text-red-600 hover:text-red-900">
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        -->
                    </div>
                </div>

            </div>

            <Modal :show="showDeleteUser" @close="closeModal" max-width="md">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">Tem certeza que deseja excluir esta conta?</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Esta ação não poderá ser desfeita. Por favor, confirme se deseja prosseguir.
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                        <DangerButton @click="deleteAccount">Sim, Excluir</DangerButton>
                    </div>
                </div>
            </Modal>

        </div>
    </AuthenticatedLayout>
</template>