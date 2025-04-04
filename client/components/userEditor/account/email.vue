<script setup lang="ts">
import { type ZodSchema, z } from "zod";
import type { User } from "~/types/models/users";
import type { Form, FormSubmitEvent } from "#ui/types";

const props = defineProps<{
  api: string;
  canEdit: boolean;
}>();

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});

const auth = useAuthStore();

const { $api } = useNuxtApp();
const toast = useToast();
const $dayjs = useDayjs();

const schema = z.object({
  email: z.string({ message: "Invalid email" }).email(),
});
type Schema = z.output<typeof schema>;

const loading = ref(false);
const form = ref<Form<Schema>>();
const state = ref<{
  email?: string;
}>({
  email: user.value?.email ?? "",
});
const verifier = ref({
  loading: false,
});

const canVerify = computed(() => {
  return (
    user.value?.email &&
    !user.value?.verified &&
    auth.username != user.value?.username
  );
});

const modified = computed(() => state.value.email != (user.value?.email ?? ""));

const onSave = async (event: FormSubmitEvent<Schema>) => {
  if (props.canEdit) {
    loading.value = true;
    return new Promise((resolve) => {
      $api
        .patch(props.api, event.data)
        .then((response) => {
          user.value = response.data.data as User;
          toast.add({
            title: "Success",
            description: response.data.message ?? "Email changed successfully",
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
  }
};

const verifyEmail = () => {
  verifier.value.loading = true;
  $api
    .patch(`/user/verify/${user.value?.id}`)
    .then((response) => {
      user.value = response.data.data as User;
      toast.add({
        title: "Success",
        description: response.data.message ?? "Email verified successfully",
        color: "primary",
        icon: "tabler:circle-check",
      });
    })
    .finally(() => {
      verifier.value.loading = false;
    });
};
</script>

<template>
  <TForm
    ref="form"
    :state
    :schema
    :validateOn="['submit']"
    class="flex items-end gap-2"
    @submit="onSave"
  >
    <TFormGroup
      label="Email"
      name="email"
      :ui="{
        wrapper: 'flex-auto',
        container: 'flex items-center gap-2 relative',
      }"
    >
      <template v-if="user?.email && user?.verified" #hint>
        <TTypography variant="xs">
          Verified at {{ $dayjs(user?.verified).format("DD MMM YYYY") }}
        </TTypography>
      </template>
      <TInput
        v-model="state.email"
        placeholder="Enter Email"
        :ui="{ wrapper: 'flex-auto', base: 'max-w-full' }"
        :disabled="!canEdit || loading"
      />
      <TButton
        v-if="canVerify"
        label="Verify"
        variant="ghost"
        color="gray"
        :loading="verifier.loading"
        :disabled="loading || user!.email != state.email"
        @click="verifyEmail"
      />
      <TButton
        v-if="canEdit"
        :label="loading ? '' : 'Save'"
        type="submit"
        class="w-14 justify-center"
        :color="!modified ? 'gray' : 'primary'"
        :loading
        :disabled="!modified"
      />
    </TFormGroup>
  </TForm>
</template>
