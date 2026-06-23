<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PurchaseForm from './Form.vue';

defineProps({
    categories: Array,
    accounts: Array
});

const form = useForm({
    description: '',
    total_amount: '',
    total_installments: 2,
    category_id: '',
    account_card_id: '',
    purchase_date: new Date().toISOString().slice(0, 10),
});

const submit = () => {
    form.post(route('installment-purchases.store'));
};
</script>

<template>

    <Head title="Nova Compra Parcelada" />

    <AuthenticatedLayout>
        <template #header-title>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova Compra Parcelada</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <form @submit.prevent="submit">
                        <PurchaseForm :form="form" :categories="categories" :accounts="accounts">
                            <template #buttons>
                                <div class="flex justify-end space-x-2 pt-4">
                                    <Link :href="route('installment-purchases.index')"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                                    Cancelar
                                    </Link>
                                    <button type="submit" :disabled="form.processing"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50">
                                        Salvar
                                    </button>
                                </div>
                            </template>
                        </PurchaseForm>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>