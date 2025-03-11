<script setup lang="ts">
import type {
  ProfileImage,
  User,
  Address as AddressType,
} from "~/types/models/users";
import UserItem from "./userItem.vue";
import Avatar from "~/components/userEditor/avatar/index.vue";

const auth = useAuthStore();
const { $api } = useNuxtApp();

const user = ref<User>();
const loading = ref(false);
const modal = ref<{
  show: boolean;
}>({
  show: false,
});
const openModal = () => {
  modal.value.show = true;
};
const items = shallowRef([
  {
    key: "profile",
    label: "Profile",
    description:
      "Make changes to your account here. Click save when you're done.",
    component: defineAsyncComponent(() => import("./tabs/profile.vue")),
  },
  {
    key: "security",
    label: "Security",
    description:
      "Change your security details here. Note! Changing your username or password will log you out.",
    component: defineAsyncComponent(() => import("./tabs/security.vue")),
  },
  {
    key: "address",
    label: "Address",
    description:
      "Make changes to your address here. You can add multiple addresses.",
    component: defineAsyncComponent(() => import("./tabs/address.vue")),
  },
]);

const loadProfile = () => {
  loading.value = true;
  $api
    .get("/auth/profile")
    .then((response) => {
      user.value = response.data.data as User;
    })
    .finally(() => {
      loading.value = false;
    });
};

watch(user, (val) => {
  if (val) {
    auth.setUser(val);
    if (auth.profile) {
      const img = val.profile?.images?.[0] ?? null;
      const addr = val.profile?.addresses?.[0] ?? null;
      auth.profile.images = img ? [img as ProfileImage] : [];
      auth.profile.addresses = addr ? [addr as AddressType] : [];
    }
  }
});

watch([() => auth.email, () => auth.verified], () => {
  if (user.value) {
    user.value.email = auth.email;
    user.value.verified = auth.verified;
  }
});

onMounted(loadProfile);
</script>

<template>
  <div v-if="user" class="px-4 py-4 lg:px-56">
    <div class="grid gap-4 md:grid-cols-6">
      <div class="md:col-span-2">
        <UserItem :user @addAvatar="openModal" />
      </div>
      <div class="md:col-span-4">
        <TTabs
          :items="items"
          class="w-full"
          unmount
          :ui="{
            list: { base: 'border border-dark-200 dark:border-steel-700' },
          }"
        >
          <template #item="{ item }">
            <component
              :is="item.component"
              :label="item.label"
              :description="item.description"
              v-model:user="user"
              :ui="{
                body: {
                  base: 'flex flex-col gap-5',
                },
                footer: {
                  base: 'flex items-center justify-end',
                  padding: 'px-0 sm:px-0 pt-4 pb-0',
                },
              }"
            />
          </template>
        </TTabs>
      </div>
    </div>

    <TModal
      v-model="modal.show"
      prevent-close
      :ui="{ width: 'w-screen-95' }"
      @close="modal.show = false"
    >
      <TCard
        :ui="{
          ring: '',
          divide: 'divide-y divide-gray-100 dark:divide-gray-800',
        }"
      >
        <template #header>
          <div class="flex w-full items-center justify-between px-6 py-2">
            <h3
              class="text-base font-semibold leading-6 text-gray-900 dark:text-white"
            >
              Upload Photo
            </h3>
            <TButton
              color="gray"
              variant="ghost"
              icon="tabler:x"
              class="-my-1"
              @click="modal.show = false"
            />
          </div>
        </template>
        <div class="h-96 p-5">
          <Avatar
            v-model:user="user"
            :api="{
              create: `auth/avatars`,
              update: `auth/avatars`,
              delete: `auth/avatars`,
            }"
          />
        </div>
      </TCard>
    </TModal>
  </div>
  <div v-else class="py-4 lg:px-56">Loading...</div>
</template>
