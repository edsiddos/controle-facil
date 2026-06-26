<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

/**
 * Parâmetros recebidos pelo componente de formulário
 * @property {Object} form - Instância do useForm do Inertia
 * @property {Array} accountCards - Lista de contas disponíveis
 * @property {Array} categories - Lista de categorias cadastradas
 */
defineProps({
    form: {
        type: Object,
        required: true
    },
    accountCards: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <InputLabel>Descrição</InputLabel>
            <TextInput v-model="form.description" type="text" placeholder="Ex: Supermercado, Salário, etc." />
            <InputError :message="form.errors.description" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <InputLabel>Valor</InputLabel>
                <TextInput v-model="form.amount" type="number" step="0.01" placeholder="0,00" />
                <InputError :message="form.errors.amount" />
            </div>

            <div>
                <InputLabel>Data da Transação</InputLabel>
                <TextInput v-model="form.transaction_date" type="date" />
                <InputError :message="form.errors.transaction_date" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <InputLabel>Conta / Cartão</InputLabel>
                <SelectInput v-model="form.account_card_id">
                    <option value="" disabled>Selecione uma conta/cartão</option>
                    <option v-for="card in accountCards" :key="card.id" :value="card.id">
                        {{ card.name }}
                    </option>
                </SelectInput>
                <InputError :message="form.errors.account_card_id" />
            </div>

            <div>
                <InputLabel>Categoria</InputLabel>
                <SelectInput v-model="form.category_id">
                    <option value="" disabled>Selecione uma categoria</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </SelectInput>
                <InputError :message="form.errors.category_id" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <InputLabel>Status</InputLabel>
                <SelectInput v-model="form.status">
                    <option value="A PAGAR">A PAGAR</option>
                    <option value="PAGO">PAGO</option>
                </SelectInput>
                <InputError :message="form.errors.status" />
            </div>

            <div>
                <InputLabel>Número da Parcela (Opcional)</InputLabel>
                <TextInput v-model="form.installment_number" type="number" min="1" placeholder="Ex: 1" />
                <InputError :message="form.errors.installment_number" />
            </div>
        </div>

        <div class="flex items-start">
            <div class="flex h-5 items-center">
                <Checkbox id="is_recurring" v-model="form.is_recurring" :checked="false" />
            </div>
            <div class="ml-3 text-sm">
                <InputLabel for="is_recurring">
                    Esta transação é recorrente? (Assinatura)
                </InputLabel>
            </div>
        </div>
    </div>
</template>