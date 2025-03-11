<script setup lang="ts">
import type { User, UserRole } from "~/types/models/users";
import RoleBadge from "@/pages/settings/roles/roleBadge.vue";
const { stringToColour } = useColors();

const props = defineProps<{
  user: User;
  canEdit: boolean;
}>();

const emit = defineEmits(["edit", "view", "toggle"]);

const image = computed(() =>
  props.user.profile?.images?.find((i) => i.primary),
);

const userColor = computed(() => {
  return stringToColour(
    props.user.profile?.full_name ?? props.user.email ?? props.user.username,
  );
});
</script>

<template>
  <TCard
    class="relative overflow-hidden"
    :class="{
      grayscale: !user.active,
    }"
  >
    <div
      class="absolute inset-x-0 top-0 h-16 opacity-70"
      :style="{ backgroundColor: userColor }"
    />
    <div class="flex h-full flex-col items-center gap-4 px-4 pb-4 pt-6">
      <div class="flex flex-col items-center gap-4">
        <TAvatar
          class="shadow-md"
          size="3xl"
          icon="tabler:user-filled"
          :src="image?.url.lg"
          alt="Avatar"
        />
        <TTypography class="text-base">
          {{ user.profile?.full_name ?? user.email ?? user.username }}
        </TTypography>
      </div>
      <!-- Badge Color should change base on color -->
      <div class="text flex flex-auto flex-wrap justify-center gap-1">
        <template v-for="role in user.roles" :key="role.id">
          <RoleBadge
            :color="(role as UserRole).color ?? '#999'"
            :label="(role as UserRole).name"
            size="sm"
            variant="subtle"
            :ui="{ rounded: 'rounded-full' }"
          />
        </template>
      </div>
      <div class="flex flex-wrap items-center justify-center gap-3">
        <TButton
          v-if="canEdit"
          color="gray"
          size="sm"
          variant="solid"
          label="Edit"
          @click="emit('edit')"
          :ui="{
            inline: 'inline-flex flex-col items-center justify-center gap-1',
          }"
        >
          <TIcon name="tabler:pencil" class="h-7 w-7" />
          <TTypography variant="xs"> Edit Profile </TTypography>
        </TButton>
        <TButton
          color="gray"
          size="sm"
          variant="solid"
          :label="user.active ? 'Deactivate' : 'Activate'"
          @click="emit('toggle')"
          :ui="{
            inline: 'inline-flex flex-col items-center justify-center gap-1',
          }"
        >
          <TIcon name="tabler:lock" class="h-7 w-7" />
          <TTypography variant="xs"> Toggle status </TTypography>
        </TButton>
      </div>
    </div>
  </TCard>
</template>
