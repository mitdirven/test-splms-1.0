import type { Record } from '@/types/records';
import { z } from 'zod'

export const useRecords = () => {
    const records = ref<Record[]>([]);
    const searchState = reactive({
        name: '',
    });

    const { search, pagination, params, loading } = useSearcher({
        api: 'records',
        limit: 12,
        method: 'get',
        onPageChange: fetchRecords,
        onSearch: (response) => response.data.data,
    });

    async function fetchRecords() {
        loading.value = true;
        const { data } = await search();
        records.value = data.data;
        loading.value = false;
    }

    // Search
    function handleSearch() {
        params.value.search_term = searchState.name;
        pagination.value.page = 1;
        search();
        fetchRecords();
    }

    // Debounce for search
    const debouncedSearch = useDebounceFn(handleSearch, 500);


    // Below includes the form, schema, state for forms
    const form = ref();

    const schema = z.object({
        title: z.string(),
        subject: z.string(),
        document_type: z.object({
            id: z.number(),
        }),
        user_id: z.number(),
    });

    const state = reactive({
        title: undefined,
        subject: undefined,
        document_type: undefined,
        user_id: 1,
    });



    // Below are the functions for create, update, delete and restore


    return {
        records,
        fetchRecords,

        // Search Reuirements
        searchState,
        search,
        pagination,
        params,
        handleSearch,
        debouncedSearch,
        loading,

        // Forms
        form,
        schema,
        state

    }
}
