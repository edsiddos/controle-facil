<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3'; // Importado o 'router' para ações independentes de formulário

// Definição das propriedades (props) recebidas do controlador do Laravel
const props = defineProps({
    accounts: Array,       // Lista de contas/cartões cadastrados
    accountTypes: Array    // Lista de tipos de conta (Ex: Conta Corrente, Poupança, Cartão)
});

// Inicialização do formulário reativo do Inertia com os campos necessários
const form = useForm({
    name: '',
    account_type_id: '',
    balance: 0,
    credit_limit: null
});

// Função para enviar o formulário de cadastro (Criação)
const submit = () => {
    form.post(route('accounts.store'), {
        onSuccess: () => form.reset() // Limpa os campos do formulário após o sucesso
    });
};

// Função para deletar uma conta existente
const deleteAccount = (id) => {
    if (confirm('Tem certeza que deseja excluir esta conta?')) {
        // Usando 'router.delete' em vez de 'form.delete' para não misturar os dados do formulário com a ação de exclusão
        router.delete(route('accounts.destroy', id));
    }
};
</script>

<template>

    <Head title="Contas e Cartões" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gerenciamento de Contas e Cartões</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Nova Conta / Cartão</h3>

                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input v-model="form.name" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select v-model="form.account_type_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="" disabled>Selecione...</option>
                                <option v-for="type in accountTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Saldo Atual / Inicial</label>
                            <input v-model="form.balance" type="number" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                        </div>

                        <div v-if="form.account_type_id == 2">
                            <label class="block text-sm font-medium text-gray-700">Limite de Crédito</label>
                            <input v-model="form.credit_limit" type="number" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                        </div>

                        <div>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 w-full"
                                :disabled="form.processing">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Minhas Carteiras</h3>

                    <div class="overflow-x-auto">
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
                                        account.account_type.name
                                        }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ account.balance
                                        }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ account.available_limit ? 'R$ ' + account.available_limit : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button @click="deleteAccount(account.id)"
                                            class="text-red-600 hover:text-red-900">
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>