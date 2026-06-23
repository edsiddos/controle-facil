<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

defineProps({
    form: {
        type: Object,
        required: true
    },
    typeCreditCard: Number,
    accountTypes: Array
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <InputLabel>Nome</InputLabel>
            <TextInput v-model="form.name" required />
            <InputError :message="form.errors.name" />
        </div>

        <div>
            <InputLabel>Tipo</InputLabel>
            <SelectInput v-model="form.account_type_id" :options="accountTypes" :colLabel="'name'" :colValue="'id'" />
            <InputError :message="form.errors.account_type_id" />
        </div>

        <div>
            <InputLabel>Saldo Atual / Inicial</InputLabel>
            <TextInput v-model="form.balance" type="number" step="0.01" required />
            <InputError :message="form.errors.balance" />
        </div>

        <div v-if="form.account_type_id == typeCreditCard">
            <InputLabel>Limite de Crédito</InputLabel>
            <TextInput v-model="form.credit_limit" type="number" step="0.01" />
            <InputError :message="form.errors.credit_limit" />
        </div>
    </div>
</template>