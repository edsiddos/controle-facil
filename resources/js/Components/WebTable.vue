<script setup>
import { onMounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    headers: Array,
    link: String,
    body: Array,
    actions: {                 // Nova prop opcional para ativar botões de ação
        type: Boolean,
        default: false
    },
    editRoute: String          // Nome da rota base de edição (ex: 'accounts.edit')
});

// Define os eventos que este componente pode enviar para o componente pai
const emit = defineEmits(['delete']);

const webDataTable = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const webTableError = ref(null);

const params = ref({
    limit: 10,
    offset: 0,
    search: ''
});

// Função auxiliar para ler chaves aninhadas (ex: 'account_type.name')
const getNestedValue = (obj, path) => {
    return path.split('.').reduce((acc, part) => acc && acc[part], obj);
};

const searchData = async () => {
    webTableError.value = null;
    loading.value = true;

    try {
        let response = await axios.get(route(props.link), { params: params.value });
        webDataTable.value = response.data.data;
        totalRecords.value = response.data.total;
    } catch (error) {
        webTableError.value = `Erro ao buscar dados: ${error.message || error}`;
    } finally {
        loading.value = false;
    }
};

// Funções de paginação
const nextPage = () => {
    if (params.value.offset + params.value.limit < totalRecords.value) {
        params.value.offset += params.value.limit;
        searchData();
    }
};

const prevPage = () => {
    if (params.value.offset - params.value.limit >= 0) {
        params.value.offset -= params.value.limit;
        searchData();
    }
};

// Monitora a pesquisa com debounce
let searchTimeout;
watch(() => params.value.search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        params.value.offset = 0;
        searchData();
    }, 350);
});

// Método público exposto para permitir que o pai recarregue a tabela se necessário
defineExpose({ searchData });

onMounted(() => {
    searchData();
});
</script>

<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center gap-4">
            <div class="w-full md:w-1/3">
                <input 
                    v-model="params.search" 
                    type="text" 
                    placeholder="Buscar..." 
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                />
            </div>
            <div v-if="loading" class="text-sm text-gray-500 italic animate-pulse">
                Carregando dados...
            </div>
        </div>

        <div v-if="webTableError != null" class="p-4 bg-red-50 text-red-700 text-sm rounded-md">
            {{ webTableError }}
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr v-for="(head_row, index) in props.headers" :key="index">
                        <th 
                            v-for="(column, key) in head_row" 
                            :key="key" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            {{ column }}
                        </th>
                        <th v-if="props.actions" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(data, index) in webDataTable" :key="index" :class="{'opacity-50': loading}">
                        
                        <td 
                            v-for="(col, bIndex) in props.body" 
                            :key="bIndex" 
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            {{ getNestedValue(data, col) ?? '-' }}
                        </td>

                        <td v-if="props.actions" class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-3">
                            <Link :href="route(props.editRoute, data.id)" class="text-blue-600 hover:text-blue-900">
                                Editar
                            </Link>
                            
                            <button @click="emit('delete', data.id)" class="text-red-600 hover:text-red-900">
                                Excluir
                            </button>
                        </td>

                    </tr>
                    
                    <tr v-if="webDataTable.length === 0 && !loading">
                        <td :colspan="props.body.length + (props.actions ? 1 : 0)" class="px-6 py-10 text-center text-sm text-gray-500">
                            Nenhum registro encontrado.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
            <span class="text-sm text-gray-700">
                Exibindo <span class="font-semibold">{{ params.offset + 1 }}</span> a 
                <span class="font-semibold">{{ Math.min(params.offset + params.limit, totalRecords) }}</span> de 
                <span class="font-semibold">{{ totalRecords }}</span> total
            </span>
            
            <div class="flex gap-2">
                <button 
                    @click="prevPage" 
                    :disabled="params.offset === 0 || loading"
                    class="px-3 py-1 bg-white border border-gray-300 text-sm font-medium rounded shadow-sm text-gray-700 disabled:opacity-50"
                >
                    Anterior
                </button>
                <button 
                    @click="nextPage" 
                    :disabled="params.offset + params.limit >= totalRecords || loading"
                    class="px-3 py-1 bg-white border border-gray-300 text-sm font-medium rounded shadow-sm text-gray-700 disabled:opacity-50"
                >
                    Próxima
                </button>
            </div>
        </div>
    </div>
</template>