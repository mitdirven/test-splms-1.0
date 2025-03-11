<script setup lang="ts">
import { z } from "zod";
import { TForm } from "#components";
import type { Form, FormErrorEvent, FormSubmitEvent } from "#ui/types";
import type { RoleItem } from "~/types/models";
import PermissionSelect from "~/components/userEditor/permissions/permissionsSelect.vue";

const { randomColor } = useColors();

const { $api } = useNuxtApp();
const toast = useToast();
const model = defineModel({
  default: null,
  type: Object as PropType<Partial<RoleItem> | null>,
});

const emit = defineEmits(["close"]);

const loading = ref(false);
const loadingMessage = ref("");
const permissionError = ref<string>();

const schema = z.object({
  name: z
    .string({ message: "Name is required" })
    .min(1, { message: "Name is required" }),
  description: z.string().optional(),
  color: z.string().refine(
    (val) => {
      if (!val) return false;
      return /^#(?:[0-9a-fA-F]{3,4}){1,2}$/i.test(val);
    },
    {
      message: "Color must be a valid hex color code",
    },
  ),
  permissions: z
    .array(
      z.object({
        id: z.string(),
        name: z.string(),
        description: z.string().nullable(),
        date: z.string(),
      }),
    )
    .optional(),
});
type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref({
  name: model.value?.name ?? "",
  description: model.value?.description ?? "",
  color: model.value?.color ?? randomColor(),
  permissions: model.value?.permissions ?? [],
});

const isEdit = computed(() => !!model.value?.id);
const showDesc = ref(false);

const saveRole = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;

    const method = isEdit.value ? "patch" : "post";
    const uri = `/roles${isEdit.value ? "/" + model.value?.id : ""}`;

    const data = {
      ...e.data,
      permissions: e.data.permissions?.map((p) => p.id) ?? [],
    };

    $api[method](uri, data)
      .then((response) => {
        model.value = response.data.data;

        toast.add({
          title: "Success",
          description: response.data.message ?? "Role saved successfully!",
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

const onError = async (e: FormErrorEvent) => {
  console.log(e);
  permissionError.value = e.errors.find(
    (err) => err.path === "permissions",
  )?.message;
};
</script>

<template>
  <TCard
    :ui="{
      base: 'max-h-screen-95 h-screen',
      ring: '',
      divide: 'divide-y divide-gray-100 dark:divide-gray-800',
      header: {
        base: 'flex w-full items-center justify-between px-4 py-2',
      },
      body: {
        base: 'flex',
      },
    }"
  >
    <template #header>
      <div class="flex w-full items-center justify-between py-2">
        <h3
          class="text-base font-semibold leading-6 text-gray-900 dark:text-white"
        >
          {{ isEdit ? "Edit" : "Add" }} Role
        </h3>
        <TButton
          color="gray"
          variant="ghost"
          icon="i-heroicons-x-mark-20-solid"
          class="-my-1"
          :disabled="loading"
          @click="emit('close')"
        />
      </div>
    </template>

    <TForm
      :schema
      :state
      :validateOn="['submit']"
      ref="form"
      class="flex flex-auto flex-col gap-4 px-4 py-2"
      @submit="saveRole"
      @error="onError"
    >
      <div class="flex items-center gap-4">
        <TFormGroup
          label="Name"
          name="name"
          :required="!modelValue?.protected"
          class="flex-auto"
        >
          <TInput
            v-model="state.name"
            placeholder="Enter Permission Name"
            autocomplete="off"
            :disabled="loading || modelValue?.protected"
            :ui="{
              base: 'max-w-full',
            }"
          />
          <template v-if="modelValue?.protected" #hint>
            <TTooltip
              text="Name change is disabled for this role"
              :ui="{ base: 'text-wrap h-auto' }"
            >
              <TIcon name="tabler:lock" />
            </TTooltip>
          </template>
        </TFormGroup>
        <div class="flex items-center gap-1">
          <TFormGroup label="Color" name="color" required>
            <TColorPicker v-model="state.color" v-slot="{ color, isDark }">
              <TButton
                id="color"
                icon="tabler:color-picker"
                class="w-full flex-wrap justify-center"
                :class="{
                  '!text-gray-200': isDark(state.color!),
                  '!text-gray-700': !isDark(state.color!),
                }"
                :disabled="loading"
                :style="{ backgroundColor: state.color }"
              />
            </TColorPicker>
          </TFormGroup>
          <TButton
            icon="tabler:file-description"
            variant="ghost"
            color="gray"
            class="self-end"
            :class="{
              'text-primary-400 dark:text-primary-400': showDesc,
            }"
            @click="showDesc = !showDesc"
          />
        </div>
      </div>
      <TCollapse v-model="showDesc">
        <TFormGroup
          label="Description"
          name="description"
          hint="Optional"
          class="px-0.5"
          :ui="{ label: { base: 'flex-auto flex items-center' } }"
        >
          <TTextarea
            v-model="state.description"
            placeholder="Enter Description"
            :disabled="loading"
            :ui="{ base: 'max-w-full scrollbar-thin mb-0.5' }"
          />
        </TFormGroup>
      </TCollapse>
      <PermissionSelect
        searchApi="/roles/permissions"
        v-model="state.permissions"
        v-model:loading="loading"
        v-model:loadingMessage="loadingMessage"
        view-selected
        :error="permissionError"
        class="flex-auto transition-all"
        :class="{
          '-mt-4': !showDesc,
        }"
      />
      <div class="flex items-center justify-end gap-4 px-4">
        <TButton type="submit" label="Submit" :disabled="loading" />
        <TButton
          variant="ghost"
          color="gray"
          label="Cancel"
          :disabled="loading"
          @click="emit('close')"
        />
      </div>
    </TForm>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>
