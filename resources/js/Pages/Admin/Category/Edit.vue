<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    category: Object
});

const form = useForm({
    name: props.category.name,
    type: props.category.type
});

const submit = () => {
    form.put(route('categories.update', props.category.id));
};
</script>

<template>
    <Head title="Editar Categoria" />

    <AuthenticatedLayout>
        <template #header-title>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Categoria</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nome da Categoria</label>
                                <input v-model="form.name" type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="D">Despesa</option>
                                    <option value="R">Receita</option>
                                </select>
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
                                Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>