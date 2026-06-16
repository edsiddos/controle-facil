<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    Headers: Array,
    Route: String,
    Body: Array
});

console.log(props);

const webDataTable = ref(null);
const params = ref({
    limit: 10,
    offset: 0,
    search: null
})

const webTableError = ref(null);

const searchData = async () => {
    webTableError.value = null;

    try {
        webDataTable.value = await axios.get(route(props.Route, params.value));
        console.log(webDataTable.value);
    } catch (error) {
        webTableError.value = `Erro ao buscar dados: ${error.message || error}`;
    }
};

onMounted(() => {
    searchData();
});

</script>

<template>

    <div v-if="webTableError != null">
        {{ webTableError }}
    </div>

    <table class="table-fixed border-collapse border border-gray-400">
        <thead>
            <tr v-for="(head_row, index) in headers" :key="index">
                <th v-for="(column, key) in head_row" :key="key" :class="column.class">
                    {{ column }}
                </th>
            </tr>
        </thead>

        <tbody>
            <tr v-for="(data, index) in webDataTable" :key="index">
                <td v-for="(col, bIndex) in body" :key="bIndex" class="border border-gray-300 p-2">
                    {{ data[col] }}
                </td>
            </tr>
        </tbody>
    </table>
</template>