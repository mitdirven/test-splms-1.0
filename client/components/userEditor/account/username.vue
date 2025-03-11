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

const { $api } = useNuxtApp();
const toast = useToast();

const schema = z.object({
  username: z
    .string({ message: "Invalid email" })
    .min(1, { message: "Username is required" })
    .regex(/^[a-zA-Z0-9][a-zA-Z0-9._-]*$/, {
      message: "Invalid username format",
    }),
});
type Schema = z.output<typeof schema>;

const loading = ref(false);
const form = ref<Form<Schema>>();
const state = ref<{
  username?: string;
}>({
  username: user.value?.username ?? "",
});

const modified = computed(() => state.value.username != user.value?.username);

const onSave = async (e: FormSubmitEvent<Schema>) => {
  if (props.canEdit) {
    loading.value = true;
    return new Promise((resolve) => {
      $api
        .patch(props.api, e.data)
        .then((response) => {
          user.value = response.data.data as User;
          toast.add({
            title: "Success",
            description:
              response.data.message ?? "Username changed successfully",
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
      label="Username"
      name="username"
      :ui="{
        wrapper: 'flex-auto',
        container: 'flex items-center gap-2 relative',
      }"
    >
      <template #label>
        <div class="flex items-center gap-2">
          <span>Username</span>
          <TTooltip
            :popper="{ placement: 'right' }"
            :ui="{ wrapper: 'block', base: '' }"
          >
            <template #text>
              <div class="space-y-2 p-3">
                <span class="font-semibold">Username Requirements</span>
                <ul
                  class="list-disc space-y-1 pl-4 font-normal text-gray-500 dark:text-gray-400"
                >
                  <li>Username must be unique</li>
                  <li>Username must contain letters</li>
                  <li>Username may contain numbers</li>
                  <li>
                    Username may contain dots( . ), hyphens( - ), and/or
                    underscores( _ )
                  </li>
                  <li>Username must start with a letter or a number</li>
                </ul>
              </div>
            </template>
            <TButton
              color="gray"
              variant="ghost"
              size="xs"
              icon="tabler:info-circle-filled"
              :ui="{ base: '!px-0 -ml-1 !text-gray-400' }"
            />
          </TTooltip>
        </div>
      </template>
      <TInput
        v-model="state.username"
        placeholder="Enter Username"
        :disabled="!canEdit || loading"
        :ui="{ wrapper: 'flex-auto', base: 'max-w-full' }"
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
