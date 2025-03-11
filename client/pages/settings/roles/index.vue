<script setup lang="ts">
import type { RoleItem } from "~/types/models/roles";
import RoleBadge from "./roleBadge.vue";

import Editor from "./editor.vue";
import Delete from "./delete.vue";

const { merge } = useModels();
const $dayjs = useDayjs();
const $guard = useGuard();

type ModalType = "Editor" | "Delete";

const { pagination, params, loading, search } = useSearcher<{ search: string }>(
  {
    api: "/roles",
    limit: 9,
    appendToUrl: true,
    onSearch: (response) => {
      roles.value = response.data.data as Array<RoleItem>;
    },
  },
);

const roles = ref<Array<RoleItem>>([]);
const modal = ref<{
  show: boolean;
  data: Partial<RoleItem> | null;
  type: ModalType;
}>({
  show: false,
  data: null,
  type: "Editor",
});

const columns = computed(() => {
  let cols = [
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
  ];
  if ($guard.canAny("roles_edit", "roles_add", "roles_delete")) {
    cols.push({
      key: "actions",
      label: "Actions",
    });
  }

  return cols;
});

const openModal = (
  data: RoleItem | null = null,
  type: ModalType = "Editor",
) => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.show = true;
};

const getActions = (
  role: RoleItem,
): Array<{
  label: string;
  icon: string;
  click: () => void;
  disabled?: boolean;
}> => {
  let acts = [];
  if ($guard.can("roles_add")) {
    acts.push({
      label: "Duplicate",
      icon: "tabler:copy",
      click: () => duplicateRole(role),
    });
  }

  if ($guard.can("roles_edit")) {
    acts.push({
      label: "Edit",
      icon: "tabler:edit",
      click: () => openModal(role, "Editor"),
    });
  }

  if ($guard.can("roles_delete")) {
    acts.push({
      label: "Delete",
      icon: "tabler:trash",
      disabled: role.protected,
      click: () => openModal(role, "Delete"),
    });
  }
  return acts;
};
const duplicateRole = (data: RoleItem) => {
  modal.value.data = {
    ...data,
    id: undefined,
    name: `${data.name} (copy)`,
    color: undefined,
    protected: false,
  };
  modal.value.type = "Editor";
  modal.value.show = true;
};

const onSave = (data: RoleItem) => {
  merge(roles.value, data);
  modal.value.show = false;
};

const onDelete = (data: RoleItem) => {
  roles.value = roles.value.filter((p) => p.id !== data.id);
  modal.value.show = false;
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
              name="search"
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
          <TButton
            v-if="$guard.can('roles_add')"
            icon="tabler:plus"
            @click="openModal(null, 'Editor')"
          >
            Add Role
          </TButton>
        </div>
      </template>

      <TTable
        :columns
        :rows="roles"
        :loading
        :ui="{
          base: 'border-none',
          th: { base: '!border-none bg-gray-50 uppercase' },
        }"
      >
        <template #name-data="{ row }">
          <RoleBadge
            :label="row.name"
            size="sm"
            variant="subtle"
            :color="row.color"
          />
        </template>
        <template #date-data="{ row }">
          {{ $dayjs(row.date).format("DD MMM YYYY") }}
        </template>

        <template #actions-data="{ row }">
          <div
            v-if="getActions(row).length > 0"
            class="flex items-center gap-2"
          >
            <TButton
              v-for="act in getActions(row)"
              :key="act.label"
              :icon="act.icon"
              :disabled="act.disabled"
              size="md"
              color="gray"
              variant="ghost"
              :ui="{
                rounded: 'rounded-full',
                color: {
                  gray: {
                    ghost: 'hover:bg-gray-200 dark:hover:bg-gray-900 ',
                  },
                },
              }"
              @click="act.click()"
            />
          </div>
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
      v-if="$guard.canAny('roles_add', 'roles_edit', 'roles_delete')"
      v-model="modal.show"
      prevent-close
      :ui="{
        width: `w-screen-95 ${modal.type === 'Delete' ? 'sm:max-w-sm' : 'sm:max-w-xl'}`,
        margin: '',
      }"
    >
      <Editor
        v-if="
          modal.type === 'Editor' && $guard.canAny('roles_add', 'roles_edit')
        "
        v-model="modal.data"
        @update:modelValue="onSave($event as RoleItem)"
        @close="modal.show = false"
      />

      <Delete
        v-if="modal.type === 'Delete' && $guard.can('roles_delete')"
        :modelValue="modal.data!"
        @delete="onDelete"
        @close="modal.show = false"
      />
    </TModal>
  </TContainer>
</template>
