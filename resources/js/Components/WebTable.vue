<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    headers: Array,
    link: String,
    body: Array,
    actions: {
        type: Boolean,
        default: false
    },
    editRoute: String
});

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

// --- COMPLEMENTOS DE PAGINAÇÃO ---

// Página atual baseada no offset e limit (Inicia em 1)
const currentPage = computed({
    get: () => Math.floor(params.value.offset / params.value.limit) + 1,
    set: (page) => {
        params.value.offset = (page - 1) * params.value.limit;
        searchData();
    }
});

// Total de páginas
const totalPages = computed(() => Math.ceil(totalRecords.value / params.value.limit) || 1);

// Gera a lista de números de páginas visíveis (ex: [1, 2, 3])
const displayedPages = computed(() => {
    const pages = [];
    const maxVisible = 5; // Quantidade de botões numéricos que deseja exibir
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages.value, start + maxVisible - 1);

    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});

// Texto reativo da paginação corrente (Evita bugs visuais quando o total é 0)
const paginationText = computed(() => {
    if (totalRecords.value === 0) return 'Exibindo 0 registros';
    const from = params.value.offset + 1;
    const to = Math.min(params.value.offset + params.value.limit, totalRecords.value);
    return `Exibindo ${from} a ${to} de ${totalRecords.value}`;
});

// --- MÉTODOS DE NAVEGAÇÃO ---

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

// Reinicia a paginação quando o limite de itens muda
watch(() => params.value.limit, () => {
    currentPage.value = 1;
});

// --- BUSCA E AUXILIARES ---
// Função auxiliar para ler chaves aninhadas (ex: 'account_type.name')
const getNestedValue = (obj, path) => {
    return path.split('.').reduce((acc, part) => acc && acc[part], obj);
};

const searchData = async () => {
    webTableError.value = null;
    loading.value = true;

    try {
        // Assume-se que 'route()' é um método global disponível (ex: Ziggy)
        let response = await axios.get(route(props.link), { params: params.value });
        webDataTable.value = response.data.data;
        totalRecords.value = response.data.total;
    } catch (error) {
        webTableError.value = `Erro ao buscar dados: ${error.message || error}`;
    } finally {
        loading.value = false;
    }
};

// Monitora a pesquisa com debounce nativo do Vue 3.4+ usando onWatcherCleanup
watch(() => params.value.search, (newSearch, oldSearch, onCleanup) => {
    const timeout = setTimeout(() => {
        currentPage.value = 1; // Reseta para a página 1 ao buscar
    }, 350);
    onCleanup(() => clearTimeout(timeout));
});

defineExpose({ searchData });

onMounted(() => {
    searchData();
});
</script>

<template>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="w-full sm:w-1/3">
                <input v-model="params.search" type="text" placeholder="Buscar..."
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <div class="flex items-center gap-3 text-sm text-gray-600 self-end sm:self-auto">
                <div v-if="loading" class="italic animate-pulse text-blue-600 font-medium">
                    Carregando dados...
                </div>

                <div class="flex items-center gap-1">
                    <span>Exibir:</span>
                    <select v-model="params.limit"
                        class="rounded-md border-gray-300 py-1 pl-2 pr-8 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <div v-if="webTableError != null" class="p-4 bg-red-50 text-red-700 text-sm rounded-md border border-red-200">
            {{ webTableError }}
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr v-for="(head_row, index) in props.headers" :key="index">
                        <th v-for="(column, key) in head_row" :key="key"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ column }}
                        </th>
                        <th v-if="props.actions"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(data, index) in webDataTable" :key="data.id || index"
                        :class="{ 'opacity-40 pointer-events-none transition-opacity': loading }">
                        <td v-for="(col, bIndex) in props.body" :key="bIndex"
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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

                    <tr v-if="typeof webDataTable == 'undefined' ||  (webDataTable.length === 0 && !loading)">
                        <td :colspan="props.body.length + (props.actions ? 1 : 0)"
                            class="px-6 py-10 text-center text-sm text-gray-500">
                            Nenhum registro encontrado.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="flex flex-col sm:flex-row justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-200 gap-4">
            <span class="text-sm text-gray-700">
                {{ paginationText }}
            </span>

            <div class="flex items-center gap-1">
                <button @click="prevPage" :disabled="currentPage === 1 || loading"
                    class="px-3 py-1.5 bg-white border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Anterior
                </button>

                <div class="hidden sm:flex items-center gap-1">
                    <button v-for="page in displayedPages" :key="page" @click="currentPage = page" :disabled="loading"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium rounded-md border transition-colors',
                            currentPage === page
                                ? 'bg-blue-600 border-blue-600 text-white'
                                : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
                        ]">
                        {{ page }}
                    </button>
                </div>

                <button @click="nextPage" :disabled="currentPage === totalPages || loading"
                    class="px-3 py-1.5 bg-white border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Próxima
                </button>
            </div>
        </div>
    </div>
</template>