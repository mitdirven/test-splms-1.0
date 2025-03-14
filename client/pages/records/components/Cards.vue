<script setup lang="ts">
const { records, searchState, pagination, search, handleSearch, fetchRecords, loading } = useRecords();
const { formatDate } = useHelper();

watch(() => pagination.page,
    async () => {
        loading.value = true;
        await search();
        loading.value = false;
    }
);

// Debounce for search
const debouncedSearch = useDebounceFn(handleSearch, 500);

watch(() => searchState.name, () => {
    debouncedSearch();
}, { deep: true });

onMounted(fetchRecords);

await fetchRecords();

</script>

<template>
    <div class="">
        <TInput v-model="searchState.name" placeholder="Search control number, title or subject"
            icon="i-heroicons-magnifying-glass-20-solid" class="w-full shadow-sm focus:ring-2 focus:ring-blue-500" />
    </div>

    <div class="px-5 flex w-full py-4">
        <div class="flex items-center justify-between w-full">
            <div class="text-sm text-gray-500 dark:text-gray-400 px-2">
                <template v-if="loading">
                    <div class="flex items-center">
                        <span class="i-heroicons-arrow-path-20-solid animate-spin mr-2"></span>
                        Fetching data...
                    </div>
                </template>
                <template v-else>
                    Showing <span class="font-medium">{{ records.length }}</span> of
                    <span class="font-medium">{{ pagination.total }}</span> results
                </template>
            </div>

            <div>
                <TPagination v-model="pagination.page" :page-count="pagination.limit" :total="pagination.total"
                    :disabled="loading" :ui="{}" />
            </div>
        </div>
    </div>
    <div class="columns-2 md:columns-3 lg:columns-3 2xl:columns-3 gap-6">
        <div v-for="record in records" :key="record.id"
            class="group relative overflow-hidden  border border-gray-200  rounded-lg shadow-sm hover:shadow-md transition-all duration-300  mb-6">

            <!-- Card Header with Citation Info -->
            <div class="bg-gray-50 dark:bg-gray-750 border-b border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-between space-x-2">
                        <span class="i-heroicons-document-text-20-solid text-blue-500 dark:text-blue-400"></span>
                        <h3 class="text-sm font-medium text-primary flex gap-x-1">
                            ID: {{ record?.id }}
                        </h3>
                    </div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex gap-x-1">

                    </h3>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 flex gap-x-1">

                    </h3>
                </div>

                <TDivider class="pb-3" />

                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">Control Number:</span>
                        <span class="font-mono text-sm bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                            {{ record?.control_number }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Issued: {{ record?.created_at ? formatDate(record.created_at.toString()) : '' }}
                    </div>
                </div>
            </div>

            <!-- Information -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Information
                </h3>

                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">Title:</span>
                        <span class="font-mono text-sm py-0.5">
                            {{ record?.title }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">Subject:</span>
                        <span class="font-mono text-sm py-0.5">
                            {{ record?.subject }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">Document Type:</span>
                        <div class="flex gap-x-1">
                            <span class="font-mono text-sm py-0.5">
                                {{ record?.document_type?.code }}
                            </span>
                            <span class="font-mono text-sm py-0.5">
                                {{ record?.document_type?.name }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">User:</span>
                    <div class="flex gap-x-1">
                        <span class="font-mono text-sm py-0.5">
                            {{ record?.user?.profile.first_name }}
                        </span>
                        <span class="font-mono text-sm py-0.5">
                            {{ record?.user?.profile?.middle_name }}
                        </span>
                        <span class="font-mono text-sm py-0.5">
                            {{ record?.user?.profile?.last_name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Attachments
                </h3>

                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
                    <div class="flex items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">File name:</span>
                        <span v-for="(file, index) in record?.files" :key="index" class="font-mono text-sm py-0.5">
                            <p>{{ file.old_name }}</p>
                            <a :href="file.file_path" download
                                class="font-mono text-sm py-0.5 text-blue-500 hover:underline"> download
                            </a>

                        </span>

                    </div>
                </div>
            </div>

            <!-- Card Footer with Actions -->
            <div
                class="bg-gray-50 dark:bg-gray-750 px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Last updated: {{ formatDate(record?.updated_at || '') }}
                </span>
                <div class="flex space-x-2">
                    <!-- <button
                        class="text-xs bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-3 py-1.5 rounded-md transition-colors flex items-center">
                        <span class="i-heroicons-printer-20-solid mr-1"></span>
                        Print
                    </button> -->
                    <TButton class="text-xs px-3 py-1.5 rounded-md transition-colors flex items-center"
                        icon="i-heroicons-arrow-right-20-solid" :trailing=true>
                        View details
                    </TButton>
                </div>
            </div>

        </div>
    </div>
</template>