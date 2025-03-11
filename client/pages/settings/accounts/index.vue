<script setup lang="ts">
import type { User } from "~/types/models/users";
import type { HasKey } from "~/types";
import UserItem from "./userItem.vue";
import Editor from "./editor.vue";
import Toggle from "~/components/userEditor/toggle/index.vue";

const { merge } = useModels();
const $guard = useGuard();
const { pagination, params, loading, search } = useSearcher<{
  search: string;
  perms: boolean;
}>({
  api: "/users",
  appendToUrl: true,
  onSearch: (response) => {
    users.value = response.data.data as Array<User>;
  },
});
const users = ref<Array<User>>([]);
const modal = ref<{
  show: boolean;
  data: User | null;
  type: string;
}>({
  show: false,
  data: null,
  type: "Editor",
});

const modalWidth = computed(() => {
  const main = "w-screen-95";
  const widths = {
    Editor: "sm:max-w-xl",
    Toggle: "sm:max-w-sm",
  } as HasKey;

  return [main, widths[modal.value.type]].join(" ");
});

const openModal = (data: User | null = null, type: string = "") => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.show = true;
};

const onNewUser = (newUser: User) => {
  merge(users.value, newUser);
};

onMounted(() => {
  params.value.perms = true;
  search();
});
</script>

<template>
  <TContainer class="block h-full w-full">
    <TCard
      class="relative h-full"
      :ui="{
        base: 'border-0',
        background: '!bg-inherit',
        divide: 'divide-y divide-gray-400/25',
        header: {
          base: 'sticky top-[calc(5rem_-_7px)] z-20 p-0 rounded-t-md bg-gray-50 dark:bg-gray-900',
        },
        footer: {
          base: 'sticky bottom-0 bg-gray-50 dark:bg-gray-900 rounded-b-md',
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
            v-if="$guard.can('users_add')"
            icon="tabler:plus"
            @click="openModal(null, 'Editor')"
          >
            Add User
          </TButton>
        </div>
      </template>
      <div
        class="grid gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5"
      >
        <template v-for="user in users" :key="user.id">
          <UserItem
            :canEdit="
              $guard.canAny(
                'users_edit-profile',
                'users_edit-account',
                'users_edit-permission',
              )
            "
            :user
            @edit="openModal(user, 'Editor')"
            @toggle="openModal(user, 'Toggle')"
          />
        </template>
      </div>
    </TCard>

    <TModal
      v-model="modal.show"
      prevent-close
      :ui="{ width: modalWidth, margin: '' }"
    >
      <Editor
        v-if="modal.type === 'Editor'"
        v-model="modal.data"
        @update:modelValue="onNewUser($event! as User)"
        @close="modal.show = false"
      />
      <Toggle
        v-else-if="modal.type === 'Toggle'"
        v-model:user="modal.data!"
        @update:user="onNewUser($event! as User)"
        @close="modal.show = false"
      />
    </TModal>
  </TContainer>
</template>
