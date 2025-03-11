<script setup lang="ts">
import type { AvatarOptions } from "~/types";

const props = defineProps<{ menus?: AvatarOptions[] }>();

const $auth = useAuthStore();

const signingOut = ref(false);

const hasName = computed(
  () => $auth.profile?.first_name && $auth.profile?.last_name,
);
const profile_name = computed(() => {
  if (hasName.value) {
    let first = $auth.profile?.first_name?.split(" ")[0];
    return `${first} ${$auth.profile?.last_name}`;
  } else {
    return $auth.username;
  }
});

const profile_name_short = computed(() => {
  if (hasName.value) {
    let first = $auth.profile?.first_name?.split(" ")[0];
    return `${first?.[0]}${$auth.profile?.last_name?.[0]}`;
  } else {
    return $auth.username?.[0];
  }
});

const avatar = computed(() => $auth.profile?.images?.find((i) => i.primary));

const _menus = computed<AvatarOptions[]>(
  () => props.menus?.filter((m) => toValue(m.hidden) !== true) || [],
);

const logout = () => {
  signingOut.value = true;
  $auth.logout().finally(() => {
    signingOut.value = false;
  });
};
</script>
<template>
  <TPopover
    :popper="{ placement: 'bottom-end' }"
    :ui="{ wrapper: 'flex items-center', width: 'w-screen-95 max-w-48' }"
  >
    <TButton
      variant="ghost"
      color="gray"
      size="lg"
      square
      class="seelct-none p-0"
      :ui="{ rounded: 'rounded-full' }"
    >
      <TAvatar
        :text="profile_name_short"
        :src="avatar?.url.md"
        size="lg"
        class="bg-gray-200 dark:bg-gray-600"
      />
    </TButton>
    <template #panel="{ close }">
      <div class="flex items-center gap-2 p-4">
        <TAvatar
          :text="profile_name_short"
          :src="avatar?.url.md"
          size="lg"
          class="bg-gray-200 dark:bg-gray-600"
        />
        <span class="leading-8">
          {{ profile_name }}
        </span>
      </div>
      <TDivider />
      <template v-if="_menus?.length! > 0">
        <div class="flex flex-col gap-2 p-4">
          <template v-for="menu in _menus" :key="menu">
            <template v-if="menu.divider">
              <TDivider />
            </template>
            <TButton
              v-else-if="Object.values(menu).some((v) => !!v)"
              :icon="menu.icon"
              :label="menu.label"
              :to="menu.to"
              color="gray"
              size="md"
              variant="ghost"
              @click="menu.action?.(), close()"
            />
          </template>
        </div>
        <TDivider />
      </template>
      <div class="flex flex-col gap-2 p-4">
        <TButton
          icon="tabler:logout"
          color="gray"
          label="Logout"
          size="md"
          variant="ghost"
          :loading="signingOut"
          @click="logout(), close()"
        />
      </div>
    </template>
  </TPopover>
</template>
