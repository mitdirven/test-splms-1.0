<script setup lang="ts">
import type { User } from "~/types/models/users";
import Avatar from "~/components/userEditor/avatar/index.vue";
import AccountEditor from "~/components/userEditor/account/index.vue";
import ProfileEditor from "~/components/userEditor/profile/index.vue";
import AddressEditor from "~/components/userEditor/address/index.vue";
import PermissionsEditor from "~/components/userEditor/permissions/index.vue";

import AddAccount from "./addAccount.vue";

const user = defineModel({
  type: Object as PropType<User | null>,
  default: null,
});

const $guard = useGuard();

const emit = defineEmits(["close"]);
const index = ref();
const canSave = ref(false);
const editor = ref();
const transitioning = ref(false);

const tab = computed({
  get: () => tabs.value.indexOf(index.value),
  set: (i: number) => {
    index.value = tabs.value[i];
  },
});
const isEdit = computed(() => !!user.value?.id);
const tabs = computed<Array<{ label: string; value: string }>>(() => {
  let t: { label: string; value: string }[] = [];
  if ($guard.can("users_edit-account")) {
    t.push({
      label: "Account",
      value: "account",
    });
  }
  if ($guard.can("users_edit-profile")) {
    t.push(
      {
        label: "Profile",
        value: "profile",
      },
      {
        label: "Address",
        value: "address",
      },
      {
        label: "Avatar",
        value: "avatar",
      },
    );
  }
  if ($guard.can("users_edit-permission")) {
    t.push({
      label: "Permissions",
      value: "permissions",
    });
  }
  return t;
});

const image = computed(() =>
  user.value?.profile?.images?.find((i) => i.primary),
);

const onNewUser = (newUser: User) => {
  user.value = newUser;
  if (
    !$guard.can([
      "users_edit-account",
      "users_edit-profile",
      "users_edit-permission",
    ])
  ) {
    emit("close");
  }
};

watch(isEdit, (val) => {
  if (val) {
    tab.value = 0;
  }
});

onMounted(() => {
  nextTick(() => {
    if (isEdit.value) {
      index.value = tabs.value[0];
      tab.value = 0;
    }
  });
});
</script>

<template>
  <TCard
    :ui="{
      base: 'relative h-screen-95 max-h-[40.5rem]',
      ring: 'ring-1 ring-gray-200 dark:ring-gray-800',
      divide: 'divide-y divide-gray-400/25',
      header: {
        padding: '!p-0',
        base: 'flex-col items-stretch',
      },
      body: {
        padding: `py-2 flex flex-col ${transitioning ? 'overflow-hidden' : 'overflow-auto'} `,
      },
      footer: {
        padding: 'px-8 py-2',
        base: 'flex items-center gap-2 justify-end',
      },
    }"
  >
    <template #header>
      <div class="flex items-center justify-between gap-2 px-8 py-4">
        <TTypography variant="md" class="font-semibold">
          Update User Account
        </TTypography>
        <TButton
          icon="tabler:x"
          variant="ghost"
          color="gray"
          :padded="false"
          :ui="{ rounded: 'rounded-full' }"
          @click="emit('close')"
        />
      </div>
    </template>
    <div class="flex items-center gap-4 px-8 pb-4">
      <TAvatar
        size="3xl"
        icon="tabler:user-filled"
        alt="Avatar"
        class="bg-gray-100 shadow-md dark:bg-gray-700"
        :src="image?.url.lg"
      />
      <div class="flex flex-col">
        <TTypography variant="h6">
          {{ user?.profile?.full_name }}
        </TTypography>
        <TTypography variant="sm">
          {{ user?.username }}
          <span v-if="user?.email" class="text-gray-500">
            ({{ user?.email }})
          </span>
        </TTypography>
      </div>
    </div>
    <div>
      <TTabs
        v-model="tab"
        :items="tabs"
        :content="false"
        :ui="{ list: { rounded: '' } }"
      />
    </div>
    <div class="flex flex-auto flex-col gap-4 px-8 py-4">
      <Transition
        enter-from-class="opacity-0 blur-md"
        leave-to-class="opacity-0 blur-md"
        enter-active-class="transition duration-300 delay-300"
        leave-active-class="transition duration-300"
        @before-enter="transitioning = true"
        @after-enter="transitioning = false"
      >
        <AccountEditor
          v-if="index?.value === 'account'"
          ref="editor"
          :api="{
            email: `/user/email/${user!.id}`,
            username: `/user/username/${user!.id}`,
            password: `/user/password/${user!.id}`,
          }"
          canUpdateEmail
          canUpdateUsername
          canUpdatePassword
          v-model:user="user"
        />
        <ProfileEditor
          v-else-if="index?.value === 'profile'"
          ref="editor"
          :api="`/user/profile/${user!.id}`"
          v-model:user="user"
          @modifying="canSave = $event"
        />
        <AddressEditor
          v-else-if="index?.value === 'address'"
          ref="editor"
          v-model:user="user"
          :api="{
            editor: `/user/address/${user?.id}`,
            delete: `/user/address/${user?.id}`,
            primary: `/user/address-primary/${user?.id}`,
          }"
          canEdit
          @modifying="canSave = $event"
        />
        <Avatar
          v-else-if="index?.value === 'avatar'"
          ref="editor"
          :api="{
            create: `/user/avatar/${user?.id}`,
            update: `/user/avatar/${user?.id}`,
            delete: `/user/avatar/${user?.id}`,
          }"
          v-model:user="user"
          @modifying="canSave = $event"
        />
        <PermissionsEditor
          v-else-if="index?.value === 'permissions'"
          ref="editor"
          v-model:user="user"
          :canGiveDirectPermissions="$guard.isSuperAdmin()"
          @modifying="canSave = $event"
        />
      </Transition>
    </div>
    <div
      v-if="!isEdit"
      class="absolute inset-0 flex items-center justify-center rounded bg-gray-400/25 backdrop-blur-sm"
    >
      <AddAccount @add="onNewUser" @close="emit('close')" />
    </div>

    <template v-if="isEdit" #footer>
      <TButton
        v-if="!!editor?.save"
        label="Save"
        :color="!canSave ? 'gray' : 'primary'"
        :disabled="!canSave"
        @click="editor?.save()"
      />
      <TButton
        v-if="!!editor?.reset"
        label="Reset"
        type="submit"
        color="gray"
        variant="solid"
        :disabled="!canSave"
        @click="editor?.reset()"
      />
      <TButton label="Close" variant="outline" @click="emit('close')" />
    </template>
  </TCard>
</template>
