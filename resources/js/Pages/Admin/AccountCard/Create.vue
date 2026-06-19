<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { watch } from 'vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    accountTypes: Array,
    typeCreditCard: Number
});

const form = useForm({
    name: '',
    account_type_id: '',
    balance: 0,
    credit_limit: null
});

watch(() => form.account_type_id, (newType) => {
    if (newType != props.typeCreditCard) {
        form.credit_limit = null;
    }
});

const submit = () => {
    form.post(route('accounts.store'));
};
</script>

<template>

    <Head title="Nova Conta / Cartão" />

    <AuthenticatedLayout>
        <template #header-title>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Adicionar Nova Conta / Cartão</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nome</label>
                                <input v-model="form.name" type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <SelectInput v-model="form.account_type_id" :options="accountTypes" :cl_label="'name'"
                                    :cl_value="'id'" />
                                <InputError class="mt-2" :message="form.errors.account_type_id" />
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700">Saldo Atual / Inicial</label>
                                <input v-model="form.balance" type="number" step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                                <InputError class="mt-2" :message="form.errors.balance" />
                            </div>

                            <div v-if="form.account_type_id == typeCreditCard">
                                <label class="block text-sm font-medium text-gray-700">Limite de Crédito</label>
                                <input v-model="form.credit_limit" type="number" step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                                <InputError class="mt-2" :message="form.errors.credit_limit" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link :href="route('accounts.index')"
                                class="bg-gray-100 text-gray-700 px-4 py-2 rounded shadow hover:bg-gray-200">
                                Cancelar
                            </Link>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
                                :disabled="form.processing">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>