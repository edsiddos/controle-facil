<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import FormInputs from './Form.vue';
import { watch } from 'vue';

const props = defineProps({
    account: Object,       // Dados da conta que está sendo editada
    accountTypes: Array,
    typeCreditCard: Number
});

// Alimenta o formulário com as informações atuais carregadas do banco de dados
const form = useForm({
    name: props.account.name,
    account_type_id: props.account.account_type_id,
    balance: props.account.balance,
    credit_limit: props.account.credit_limit
});

watch(() => form.account_type_id, (newType) => {
    if (newType != props.typeCreditCard) {
        form.credit_limit = null;
    }
});

const submit = () => {
    // Atualização utilizando rota PUT com o ID do modelo
    form.put(route('accounts.update', props.account.id));
};
</script>

<template>

    <Head title="Editar Conta / Cartão" />

    <AuthenticatedLayout>
        <template #header-title>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Conta / Cartão</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">

                        <FormInputs :form="form" :accountTypes="accountTypes" :typeCreditCard="typeCreditCard" />

                        <div class="flex justify-end gap-3">
                            <Link :href="route('accounts.index')"
                                class="bg-gray-100 text-gray-700 px-4 py-2 rounded shadow hover:bg-gray-200">
                                Cancelar
                            </Link>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
                                :disabled="form.processing">
                                Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>