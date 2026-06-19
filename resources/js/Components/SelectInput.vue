<script setup>
import { computed } from 'vue';

// Define o binding bidirecional (v-model) nativo do Vue 3
const modelValue = defineModel({
    type: [String, Number, Boolean, Object],
    default: ''
});

// Definição das propriedades do componente
// options recebe um array com os elementos a ser exibido
// placeholder o texto de informação
// cl_value nome do objeto de onde extrai o value das options
// cl_label nome do objeto de onde extrai o label 'texto' das options
// CASO NÃO SEJA INFORMADO VIA options PODE SER COLOCADO DIRETO VIA HTML OPTIONS
const props = defineProps({
    options: {
        type: Array,
        // Formato esperado: [{ value: 1, label: 'Opção 1' }, { value: 2, label: 'Opção 2' }]
    },
    placeholder: {
        type: String,
        default: 'Selecione uma opção...'
    },
    cl_value: {
        type: String,
        default: 'value'
    },
    cl_label: {
        type: String,
        default: 'label'
    }
});

</script>

<template>
    <select v-model="modelValue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <template v-if="options != null && options.length > 0">
            <option value="" disabled>
                {{ placeholder }}
            </option>

            <option v-for="option in options" :key="option[cl_value]" :value="option[cl_value]">
                {{ option[cl_label] }}
            </option>
        </template>
        <slot v-else></slot>
    </select>
</template>