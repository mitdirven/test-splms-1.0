<script setup lang="ts">
const { router } = useHelper();

const RecordCards = defineAsyncComponent(() => import('@/pages/records/components/Cards.vue'));
const Spinner = defineAsyncComponent(() => import('@/components/Spinner.vue'));

</script>

<template>
    <div class="max-w-6xl mx-auto w-full">
        <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <TIcon name="i-heroicons-document" class="w-7 h-7 text-primary" />
                    <h2 class="text-xl font-bold text-primary-900">Records </h2>
                </div>

                <TButton label="Create New Record" icon="i-heroicons-pencil-square"
                    @click="router.push({ name: 'create-record' })" />
            </div>
        </div>

        <TDivider class=" py-2" />

        <Suspense>
            <RecordCards />
            <template #fallback>
                <Spinner />
            </template>
        </Suspense>
    </div>
</template>