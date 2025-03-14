
import type { CRUDableComposable, ItemList, PaginatedList } from "@/types/crudables";

export function useCrudable<T>(endpoint: string, name: string) : CRUDableComposable<T>
{

    const { $api } = useNuxtApp();

    async function create(payload: object) : Promise<object> 
    {
        const response = await $api.post(endpoint, payload)
        return response.data[name] // standard naming convention
    }

    async function update(hash: string, payload: object) : Promise<object>
    {
        console.log("response update", hash, endpoint)
        const response = await $api.put(`${endpoint}/${hash}`, payload)
        return response.data[name]
    }

    async function list(payload?: object) : Promise<PaginatedList<T>>
    {
        const response = await $api.get(`${endpoint}`, { params: payload ?? null})
        return response.data
    }

    async function listAll(payload?: object) : Promise<ItemList<T>>
    {
        console.log("endpoint", endpoint, payload);
        const response = await $api.get(`${endpoint}/all`, { params: payload ?? null})
        console.log("response listAll", response.data);
        return response
    }


    async function updateMany(payload?: object) : Promise<ItemList<T>>
    {
        console.log("payload", payload);
        const response = await $api.put(`${endpoint}/updateMany`, payload)
        console.log("response", response)
        return response.data[name]
    }


    async function show(hash: string, query_parameters?:  object) : Promise<object>
    {
        const response = await $api.get(`${endpoint}/${hash}`, {params: query_parameters ?? null});
        return response.data
    }

    async function destroy(hash: string) : Promise<boolean>
    {
        console.log("response destroy", hash, endpoint)
        const response = await $api.delete(`${endpoint}/${hash}`)
        return response.status === 200
    }

    async function restore(hash: string) : Promise<boolean>
    {
        const response = await $api.patch(`${endpoint}/${hash}`,);
        return response.status === 200
    }

    return {
        create, 
        update, 
        list,
        listAll,
        updateMany,
        show,
        destroy, 
        restore,
        $api
    }

}
