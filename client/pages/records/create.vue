<script setup lang="ts">

import type { FormSubmitEvent } from '#ui/types'
import type { DocumentType } from '@/types/document_types';
import type { Schema } from '@/types/records';

const { form, schema, state, loading } = useRecords();
const { documentTypeData, documentTypeFetch } = useDocumentTypes();

const { router } = useHelper();

onMounted(documentTypeFetch);

// Submit handler that properly formats the data for the backend
async function onSubmit(event: FormSubmitEvent<Schema>) {
    loading.value = true;
    const crudable = useCrudable<DocumentType>('records', 'record');
    await crudable.create(event.data);
    loading.value = false;
    router.push({ name: 'index-records' })
}
</script>

<template>
    <div class="max-w-6xl mx-auto w-full">
        <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <TIcon name="i-heroicons-document" class="w-7 h-7 text-primary" />
                    <h2 class="text-xl font-bold text-primary-900">Create Record</h2>
                </div>
            </div>
        </div>

        <div>
            <TForm ref="form" :schema="schema" :state="state" class="space-y-4" @submit="onSubmit">
                <div class="flex py-5">
                    <div class="basis-1/3 px-4">
                        <TFormGroup name="title" label="Title" class="mb-6">
                            <TInput v-model="state.title" />
                        </TFormGroup>
                    </div>

                    <TDivider orientation="vertical" />

                    <div class="basis-1/3 px-4">
                        <TFormGroup name="subject" label="Subject" class="mb-6">
                            <TInput v-model="state.subject" />
                        </TFormGroup>
                    </div>

                    <TDivider orientation="vertical" />

                    <div class="basis-1/3 px-4">
                        <TFormGroup name="document_type" label="Select Document Type">
                            <TInputMenu placeholder="Select Document Type" option-attribute="name"
                                v-model="state.document_type" :options="documentTypeData" />
                        </TFormGroup>

                        <TFormGroup name="user_id" label="User ID" class="mb-6" hidden>
                            <TInput v-model="state.user_id" />
                        </TFormGroup>
                    </div>
                </div>

                <TButton type="submit" :loading="loading">
                    Submit
                </TButton>
            </TForm>
        </div>
    </div>
</template>