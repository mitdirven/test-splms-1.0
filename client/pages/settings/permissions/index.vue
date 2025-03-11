<script setup lang="ts">
import type { PermissionItem } from "~/types/models/permission";
import Editor from "./editor.vue";
import Delete from "./delete.vue";

const { dashToHuman } = useUtils();
const { merge } = useModels();
const $dayjs = useDayjs();
const $route = useRoute();

type ModalType = "Editor" | "Delete";

const { pagination, params, loading, search } = useSearcher<{ search: string }>(
  {
    api: "/permissions",
    limit: 9,
    appendToUrl: true,
    onSearch: (response) => {
      permissions.value = response.data.data as Array<PermissionItem>;
    },
  },
);

const columns = ref([
  {
    key: "id",
    label: "ID",
  },
  {
    key: "name",
    label: "Name",
  },
  {
    key: "description",
    label: "Description",
  },
  {
    key: "date",
    label: "Created At",
  },
  {
    key: "actions",
    label: "Actions",
  },
]);

const permissions = ref<Array<PermissionItem>>([]);
const modal = ref<{
  open: boolean;
  data: PermissionItem | null;
  type: ModalType;
}>({
  open: false,
  data: null,
  type: "Editor",
});

const onSave = (data: PermissionItem) => {
  merge(permissions.value, data);
  modal.value.open = false;
};

const openModal = (
  data: PermissionItem | null = null,
  type: ModalType = "Editor",
) => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.open = true;
};

const onDelete = (data: PermissionItem) => {
  permissions.value = permissions.value.filter((p) => p.id !== data.id);
  modal.value.open = false;
};

onMounted(() => {
  search();
});
</script>

<template>
  <TContainer class="block w-full">
    <TCard
      class="relative h-full"
      :ui="{
        divide: 'divide-y divide-gray-400/25',
        header: {
          base: 'sticky top-[calc(5rem_-_7px)] z-20 p-0 rounded-t-md bg-gray-50 dark:bg-gray-800',
        },
        footer: {
          base: 'sticky bottom-0 bg-gray-50 dark:bg-gray-800 rounded-b-md',
        },
      }"
    >
      <template #header>
        <div class="flex flex-auto items-center justify-between px-3 py-3.5">
          <div class="flex items-center gap-4">
            <TInput
              v-model="params.search"
              size="md"
              color="white"
              trailing-icon="tabler:search"
              placeholder="Search..."
              :ui="{
                icon: { trailing: { pointer: '', padding: { md: 'px-0' } } },
              }"
              class="flex-auto"
              @keyup.enter="search"
            >
              <template #trailing>
                <TButton
                  icon="tabler:search"
                  :loading
                  color="gray"
                  size="md"
                  variant="link"
                  class="px-3"
                  @click="search"
                />
              </template>
            </TInput>
          </div>
          <TButton icon="tabler:plus" @click="openModal(null, 'Editor')">
            Add Permission
          </TButton>
        </div>
      </template>
      <TTable
        :columns="columns"
        :rows="permissions"
        :loading
        :ui="{
          base: 'border-none',
          th: { base: '!border-none bg-gray-50 uppercase' },
          td: { base: 'w-fit' },
        }"
      >
        <template #name-data="{ row }">
          {{ dashToHuman(row.name) }}
        </template>

        <template #date-data="{ row }">
          {{ $dayjs(row.date).format("DD MMM YYYY") }}
        </template>

        <template #actions-data="{ row }">
          <div class="flex items-center">
            <TButton
              icon="tabler:edit"
              color="gray"
              size="md"
              variant="ghost"
              :ui="{
                color: {
                  gray: {
                    ghost:
                      'hover:bg-gray-200 dark:hover:bg-gray-900 rounded-full',
                  },
                },
              }"
              @click="openModal(row as PermissionItem, 'Editor')"
            />
            <TButton
              icon="tabler:trash"
              color="gray"
              size="md"
              variant="ghost"
              :ui="{
                color: {
                  gray: {
                    ghost:
                      'text-coral-500 hover:text-coral-500 dark:text-coral-400 hover:bg-coral-100 dark:hover:bg-coral-400/80 rounded-full',
                  },
                },
              }"
              @click="openModal(row as PermissionItem, 'Delete')"
            />
          </div>
        </template>

        <template #description-data="{ row }">
          <span class="inline-block max-w-[400px] text-wrap">{{
            row.description
          }}</span>
        </template>
      </TTable>
      <TInnerLoading :active="loading" text="Fetching permissions..." />
      <template #footer>
        <div class="flex justify-end bg-gray-50 dark:bg-gray-800">
          <TPagination
            v-model="pagination.page"
            :total="pagination.total"
            :pageCount="pagination.limit"
          />
        </div>
      </template>
    </TCard>
    <TModal
      v-model="modal.open"
      :prevent-close="loading"
      :ui="{ width: 'w-screen-95 max-w-sm sm:max-w-sm' }"
    >
      <Editor
        v-if="modal.type === 'Editor'"
        v-model="modal.data"
        @update:modelValue="onSave($event as PermissionItem)"
        @close="modal.open = false"
      />

      <Delete
        v-if="modal.type === 'Delete'"
        :modelValue="modal.data!"
        @delete="onDelete"
        @close="modal.open = false"
      />
    </TModal>
  </TContainer>
</template>
