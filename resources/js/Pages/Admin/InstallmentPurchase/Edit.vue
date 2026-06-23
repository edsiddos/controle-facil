<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PurchaseForm from './Form.vue';

const props = defineProps({
    purchase: Object,
    categories: Array,
    accounts: Array
});

const form = useForm({
    description: props.purchase.description,
    total_amount: props.purchase.total_amount,
    total_installments: props.purchase.total_installments,
    category_id: props.purchase.category_id,
    account_card_id: props.purchase.account_card_id,
    purchase_date: props.purchase.purchase_date ? props.purchase.purchase_date.substring(0, 10) : '',
});

const submit = () => {
    form.put(route('installment-purchases.update', props.purchase.id));
};
</script>

<template>

    <Head title="Editar Compra Parcelada" />

    <AuthenticatedLayout>
        <template #header-title>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Compra Parcelada</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 text-sm rounded">
                        <strong>Aviso:</strong> Alterar dados financeiros estruturais pode demandar ajustes manuais nas
                        transações filhas dependendo da regra aplicada.
                    </div>

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