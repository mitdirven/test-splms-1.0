<script setup lang="ts">
import { useFocusTrap } from "@vueuse/integrations/useFocusTrap";
import { z } from "zod";
import type { HasKey } from "~/types";
import type { BarangayType } from "~/types/models/address";
import type { Address, User } from "~/types/models/users";
import type { Form, FormSubmitEvent } from "#ui/types";

const props = defineProps<{
  address?: Address | null;
  api: string;
}>();

const emit = defineEmits(["close"]);

const { $api } = useNuxtApp();

const toast = useToast();

const user = defineModel({
  type: Object as PropType<User | null>,
});

const target = ref();
const loading = ref(false);
const schema = z.object({
  barangay: z.object(
    {
      code: z.string({ message: "Barangay is required" }),
    },
    {
      required_error: "Barangay is required",
    },
  ),
  location: z
    .string({ message: "Address is required" })
    .min(1, { message: "Address is required" }),
  zipCode: z.number({ message: "Zip Code is required" }).max(9999, {
    message: "Zip Code must be 4 digits",
  }),
});
type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref<
  HasKey & {
    barangay?: BarangayType;
    location?: string;
    zipCode?: number;
  }
>({
  barangay: props.address?.barangay as BarangayType,
  location: props.address?.location,
  zipCode: Number(props.address?.zipCode) || undefined,
});

const { hasFocus, activate, deactivate } = useFocusTrap(target);

const isEdit = computed(() => !!props.address?.id);
const saveProfile = (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve) => {
    loading.value = true;
    const method = isEdit.value ? "patch" : "post";

    let uri = props.api;

    if (isEdit.value) {
      uri += `/${props.address?.id}`;
    }
    $api[method](
      uri,
      Object.assign({}, e.data, { barangay: e.data.barangay.code }),
    )
      .then((response) => {
        user.value = response.data.data as User;

        toast.add({
          title: "Profile Updated",
          description:
            response.data.message ?? "User Address saved successfully",
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

onMounted(() => {
  nextTick(() => {
    activate();
  });
});

onBeforeUnmount(() => {
  deactivate();
});
</script>

<template>
  <TForm
    ref="form"
    :state
    :schema
    :validateOn="['submit']"
    @submit="saveProfile"
  >
    <TCard
      ref="target"
      :ui="{
        base: 'w-screen-95 max-w-sm',
        divide: 'divide-y divide-gray-400/25',
        header: { base: 'flex items-center gap-2', padding: 'px-3 py-1' },
        body: { base: 'flex flex-col gap-5', padding: 'px-3 pt-2 pb-6' },
        footer: {
          base: ' justify-end flex items-center gap-2',
          padding: 'px-3 py-2',
        },
      }"
    >
      <template #header>
        <TTypography variant="h6" class="flex-auto">
          Address Editor
        </TTypography>
        <TButton
          icon="tabler:x"
          size="xs"
          variant="ghost"
          color="gray"
          :padded="false"
          :ui="{ rounded: 'rounded-full' }"
          @click="emit('close')"
        />
      </template>
      <TBarangay v-model="state.barangay" />
      <TFormGroup
        label="Postal Code"
        name="zipCode"
        required
        class="col-span-full"
      >
        <TInput
          v-model.number="state.zipCode"
          placeholder="Enter Postal Code"
          autocomplete="off"
          type="number"
          :disabled="loading"
          :ui="{ base: 'max-w-full appearance-none' }"
        />
      </TFormGroup>
      <TFormGroup
        label="Address"
        name="location"
        required
        class="col-span-full"
      >
        <TInput
          v-model="state.location"
          placeholder="Enter Address"
          autocomplete="off"
          :disabled="loading"
          :ui="{ base: 'max-w-full' }"
        />
      </TFormGroup>
      <template #footer>
        <TButton type="submit" label="Save" size="sm" :loading="loading" />
        <TButton
          label="Cancel"
          size="sm"
          color="gray"
          variant="ghost"
          @click="emit('close')"
        />
      </template>
    </TCard>
  </TForm>
</template>
