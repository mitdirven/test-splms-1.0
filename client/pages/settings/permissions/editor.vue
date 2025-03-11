<script setup lang="ts">
import { z } from "zod";
import type { Form, FormSubmitEvent } from "#ui/types";
import type { PermissionItem } from "~/types/models/permission";

const { $api } = useNuxtApp();
const { dashToHuman } = useUtils();
const toast = useToast();

const model = defineModel({
  default: null,
  type: Object as PropType<PermissionItem | null>,
});

const emit = defineEmits(["close"]);

const pattern = /[^:a-z_-]{1,}/g;
const loading = ref<boolean>(false);
const debounce = ref<NodeJS.Timeout | null>(null);
const schema = z.object({
  name: z
    .string({ message: "Name is required" })
    .min(1, { message: "Name is required" })
    .refine((val) => !pattern.test(val), {
      message: "Name can only contain letters, underscores, dashes and colons",
    }),
  description: z.string().optional().nullable(),
});
type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref({
  name: model.value?.name,
  description: model.value?.description,
});

const isEdit = computed(() => !!model.value?.id);

const savePermission = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;

    const method = isEdit.value ? "patch" : "post";
    const uri = `/permissions${isEdit.value ? "/" + model.value?.id : ""}`;

    $api[method](uri, e.data)
      .then((response) => {
        model.value = response.data.data;

        toast.add({
          title: "Success",
          description:
            response.data.message ?? "Permission saved successfully!",
          color: "primary",
          icon: "tabler:circle-check",
        });

        resolve(response);
      })
      .catch((error) => {
        const errors = Object.keys(error.response.data.errors).map((k) => {
          return { path: k, message: error.response.data.errors[k][0] };
        });
        form.value?.setErrors(errors);
      })
      .finally(() => {
        loading.value = false;
      });
  });
};

const onChange = (e: KeyboardEvent) => {
  if (debounce.value) {
    clearTimeout(debounce.value);
  }

  debounce.value = setTimeout(() => {
    const rawValue = (e.target as HTMLInputElement).value;
    state.value.name = rawValue
      .toLowerCase()
      .replace(/\s/g, "_")
      .replace(pattern, "");
  }, 50);
};
</script>

<template>
  <TCard
    :ui="{
      ring: '',
      divide: 'divide-y divide-gray-100 dark:divide-gray-800',
      header: {
        base: 'flex w-full items-center justify-between px-4 py-2',
      },
    }"
  >
    <template #header>
      <h3
        class="text-base font-semibold leading-6 text-gray-900 dark:text-white"
      >
        {{ isEdit ? "Edit" : "Add" }} Permission
      </h3>
      <TButton
        color="gray"
        variant="ghost"
        icon="i-heroicons-x-mark-20-solid"
        class="-my-1"
        :disabled="loading"
        @click="emit('close')"
      />
    </template>
    <div class="px-4 py-2">
      <TForm
        :schema
        :state
        :validateOn="['submit']"
        ref="form"
        class="space-y-4"
        @submit="savePermission"
      >
        <TFormGroup
          label="Name"
          name="name"
          :help="dashToHuman(state.name ?? '')"
        >
          <TInput
            v-model="state.name"
            placeholder="Enter Permission Name"
            @input="onChange"
            :disabled="loading"
            :ui="{
              base: 'max-w-full',
            }"
          />
        </TFormGroup>
        <TFormGroup label="Description" name="description">
          <TTextarea
            v-model="state.description"
            placeholder="Enter Description"
            :disabled="loading"
            :ui="{
              base: 'max-w-full scrollbar-thin h-32',
            }"
          />
        </TFormGroup>
        <div class="flex items-center justify-end gap-4">
          <TButton type="submit" label="Submit" :disabled="loading" :loading />
          <TButton
            variant="ghost"
            color="gray"
            label="Cancel"
            :disabled="loading"
            @click="emit('close')"
          />
        </div>
      </TForm>
    </div>
  </TCard>
</template>
