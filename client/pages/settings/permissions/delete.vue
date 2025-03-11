<script setup lang="ts">
import type { PermissionItem } from "~/types/models/permission";

const { dashToHuman } = useUtils();
const { $api } = useNuxtApp();
const toast = useToast();

const props = defineProps<{
  modelValue: PermissionItem;
}>();

const emit = defineEmits(["delete", "close"]);

const loading = ref(false);

const deletePermission = () => {
  loading.value = true;
  $api
    .delete(`/permissions/${props.modelValue.id}`)
    .then((response) => {
      toast.add({
        title: "Success",
        description:
          response.data.message ?? "Permission deleted successfully!",
        color: "primary",
        icon: "tabler:circle-check",
      });
      emit("delete", props.modelValue);
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>

<template>
  <TCard
    :ui="{
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
        class="flex flex-auto flex-col items-center text-sunset-500 dark:text-sunset-400"
      >
        <TIcon name="tabler:alert-triangle" class="h-16 w-16" />
        <h3 class="flex-auto text-center text-base font-semibold leading-6">
          Are you sure you want to delete this permission?
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
    <div class="flex flex-col gap-2 px-12 py-2">
      <TTypography variant="sm">
        This will permanently delete the selected permission
        <span class="font-bold underline">
          "{{ dashToHuman(modelValue.name) }}"
        </span>
      </TTypography>
      <TTypography variant="sm" class="font-bold">
        This cannot be undone!
      </TTypography>
    </div>

    <TInnerLoading :active="loading" text="Deleting permission..." />

    <template #footer>
      <TButton
        label="Delete"
        color="sunset"
        :disabled="loading"
        :loading
        @click="deletePermission"
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
