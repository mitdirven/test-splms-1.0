<script setup lang="ts">
import type { User, Address } from "~/types/models/users";
const props = defineProps<{
  address: Address;
  api: string;
  canEdit: boolean;
}>();

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});

const { $api } = useNuxtApp();
const toast = useToast();

const emit = defineEmits(["edit", "delete"]);

const loading = ref(false);

const subAddress = computed(() => {
  return [
    props.address.barangay?.name,
    props.address.city?.name,
    props.address.province?.name,
    props.address.region?.name,
    props.address.zipCode,
  ]
    .filter(Boolean)
    .join(", ");
});

const setDefault = () => {
  loading.value = true;
  $api
    .patch(`${props.api}/${props.address.id}`)
    .then((response) => {
      user.value = response.data.data as User;
      toast.add({
        title: "Success",
        description: response.data.message ?? "Address set as default",
        color: "primary",
        icon: "tabler:circle-check",
      });
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>

<template>
  <div
    class="flex flex-col items-center gap-4 rounded bg-light-50 px-4 shadow-md dark:bg-gray-700 md:flex-row"
  >
    <div class="flex-auto space-y-2">
      <TTypography variant="span" class="text-base">
        {{ address.location }}
      </TTypography>
      <TTypography variant="sm" class="leading-none text-gray-400">
        {{ subAddress }}
      </TTypography>
    </div>
    <div v-if="canEdit" class="grid gap-2">
      <div class="flex items-center">
        <TButton
          label="Edit"
          size="sm"
          variant="ghost"
          color="gray"
          icon="tabler:pencil"
          class="flex-auto justify-center"
          :class="{
            'col-span-full': address.isMain,
          }"
          :ui="{ padding: { sm: 'px-1.5 py-1' } }"
          @click="emit('edit')"
        />
        <TButton
          v-if="!address.isMain"
          label="Delete"
          size="sm"
          variant="ghost"
          color="gray"
          icon="tabler:trash"
          class="flex-auto justify-center text-coral-500 hover:text-coral-500 dark:text-coral-400"
          :ui="{ padding: { sm: 'px-1.5 py-1' } }"
          @click="emit('delete')"
        />
      </div>
      <TButton
        v-if="!address.isMain"
        :label="loading ? 'Saving' : 'Set Default'"
        size="sm"
        variant="outline"
        color="gray"
        class="min-w-fit justify-center text-center"
        :loading
        @click="setDefault"
      />
    </div>
  </div>
</template>
