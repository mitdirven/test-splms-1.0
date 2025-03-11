<script setup lang="ts">
import type { User } from "~/types/models/users";

const user = defineModel("user", {
  type: Object as PropType<User>,
  default: null,
});

const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref(false);

const image = computed(() =>
  user.value.profile?.images?.find((i) => i.primary),
);

const toggleUser = () => {
  loading.value = true;
  $api
    .delete(`/user/toggle/${user.value.id}`)
    .then((response) => {
      user.value = response.data.data as User;
      toast.add({
        title: "Success",
        description:
          response.data.message ??
          `User ${user.value.active ? "enabled" : "disabled"} successfully`,
        color: "primary",
        icon: "tabler:circle-check",
      });
      emit("close");
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>
<template>
  <TCard
    ref="target"
    :ui="{
      base: 'w-screen-95 max-w-sm',
      ring: '',
      divide: 'divide-y divide-gray-400/25',
      header: {
        base: 'flex w-full relative items-center justify-between',
        padding: 'px-4 py-2',
      },
      footer: {
        base: 'flex items-center justify-end gap-2',
        padding: 'px-4 py-2',
      },
    }"
  >
    <template #header>
      <div
        class="flex flex-auto flex-col items-center"
        :class="{
          'text-pine-500 dark:text-pine-400': !user.active,
          'text-sunset-500 dark:text-sunset-400': user.active,
        }"
      >
        <TIcon name="tabler:alert-triangle" class="h-16 w-16" />
        <h3 class="flex-auto text-center text-base font-semibold leading-6">
          Are you sure you want to
          {{ user.active ? "disable" : "enable" }} this user?
        </h3>
      </div>
      <span class="absolute right-2 top-2">
        <TButton
          color="gray"
          variant="ghost"
          icon="i-heroicons-x-mark-20-solid"
          class="-my-1"
          :disabled="loading"
          @click="emit('close')"
        />
      </span>
    </template>
    <div class="flex flex-col gap-2 px-5 py-2 text-center">
      <TTypography variant="sm" class="font-medium">
        This will {{ user?.active ? "prevent" : "allow" }} the user
        {{ user?.active ? "from logging in" : "to login" }}: <br />

        <div
          class="my-3 flex items-center gap-4 rounded bg-coral-100 p-1 font-bold dark:bg-gray-400/25"
        >
          <TAvatar
            size="3xl"
            icon="tabler:user-filled"
            alt="Avatar"
            class="bg-gray-100 shadow-md dark:bg-gray-700"
            :src="image?.url.lg"
            :ui="{ rounded: 'rounded-lg' }"
          />
          <div class="flex flex-col text-start">
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
      </TTypography>
    </div>

    <TInnerLoading :active="loading" text="Deleting address..." />

    <template #footer>
      <TButton
        :label="user.active ? 'Disable' : 'Enable'"
        :color="user.active ? 'sunset' : 'pine'"
        :disabled="loading"
        :loading
        @click="toggleUser"
      />
      <TButton
        label="Cancel"
        variant="ghost"
        color="gray"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
  </TCard>
</template>
