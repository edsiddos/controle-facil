<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    type: 'D'
});

const options = [{ value: 'D', label: 'DESPESA' }];

const submit = () => {
    form.post(route('categories.store'));
};
</script>

<template>

    <Head title="Nova Categoria" />

    <AuthenticatedLayout>
        <template #header-title>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Adicionar Nova Categoria</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel>Nome da Categoria</InputLabel>
                                <TextInput type="text" required v-model="form.name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel>Tipo</InputLabel>
                                <SelectInput v-model="form.type" required>
                                    <option value="D">Despesa</option>
                                    <option value="R">Receita</option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors.type" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link :href="route('categories.index')"
                                class="bg-gray-100 text-gray-700 px-4 py-2 rounded shadow hover:bg-gray-200">
                                Cancelar
                            </Link>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
                                :disabled="form.processing">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>