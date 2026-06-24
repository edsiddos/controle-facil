<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    form: Object,
    categories: Array,
    accounts: Array
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <InputLabel>Descrição</InputLabel>
            <TextInput v-model="form.description" required />
            <InputError :message="form.errors.description" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <InputLabel>Valor Total (R$)</InputLabel>
                <TextInput v-model="form.total_amount" type="number" step="0.01" min="0" required />
                <InputError :message="form.errors.total_amount" />
            </div>
            <div>
                <InputLabel>Nº de Parcelas</InputLabel>
                <TextInput v-model="form.total_installments" type="number" min="2" required />
                <InputError :message="form.errors.total_installments" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <InputLabel>Categoria</InputLabel>
                <SelectInput v-model="form.category_id" required>
                    <option value="">Selecione...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </SelectInput>
                <InputError :message="form.errors.category_id" />
            </div>
            <div>
                <InputLabel>Conta / Cartão</InputLabel>
                <SelectInput v-model="form.account_card_id" required>
                    <option value="">Selecione...</option>
                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
                </SelectInput>
                <InputError :message="form.errors.account_card_id" />
            </div>
        </div>

        <div>
            <InputLabel>Data da Compra</InputLabel>
            <TextInput v-model="form.purchase_date" required />
            <InputError :message="form.errors.purchase_date" />
        </div>

        <slot name="buttons"></slot>
    </div>
</template>