<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

defineProps({
    form: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <InputLabel>Nome da Categoria</InputLabel>
            <TextInput type="text" required v-model="form.name" />
            <InputError :message="form.errors.name" />
        </div>

        <div>
            <InputLabel>Tipo</InputLabel>
            <SelectInput v-model="form.type" required>
                <option value="D">Despesa</option>
                <option value="R">Receita</option>
            </SelectInput>
            <InputError :message="form.errors.type" />
        </div>

        <div>
            <InputLabel>Icone</InputLabel>
            <SelectInput v-model="form.icon">
                <option value="">Sem Ícone</option>
                <option value="💰">💰 Dinheiro / Salário</option>
                <option value="🛒">🛒 Supermercado</option>
                <option value="🚗">🚗 Transporte / Carro</option>
                <option value="🏠">🏠 Moradia / Aluguel</option>
                <option value="🍔">🍔 Alimentação / Restaurante</option>
                <option value="💡">💡 Contas / Luz / Água</option>
                <option value="🏥">🏥 Saúde / Farmácia</option>
                <option value="🎉">🎉 Lazer / Entretenimento</option>
            </SelectInput>
            <InputError :message="form.errors.icon" />
        </div>
    </div>
</template>