<script setup lang="ts">
import { useFocusTrap } from "@vueuse/integrations/useFocusTrap";
import { z } from "zod";
import { TForm } from "#components";
import type { Form, FormSubmitEvent } from "#ui/types";
import type { User } from "~/types/models/users";
import type { Common } from "~/types/models";

const emit = defineEmits(["add", "close"]);

const { $api } = useNuxtApp();
const toast = useToast();
const { strength, passwordRules, generator } = usePassword();

const target = ref();
const loading = ref(false);
const roles = ref<Array<Common>>([]);
const schema = z.object({
  username: z
    .string({ message: "Username is required" })
    .min(1, { message: "Username is required" })
    .regex(/^[a-zA-Z0-9][a-zA-Z0-9._-]*$/, {
      message: "Invalid username format",
    }),
  email: z.union([z.literal(""), z.string().email()]),
  password: passwordRules(),
  roles: z.array(z.string()).min(1, { message: "Roles is required" }),
});

type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref<{
  username: string;
  email?: string;
  password: string;
  roles: Array<string>;
}>({
  username: "",
  email: "",
  password: "",
  roles: [],
});

const { activate, deactivate } = useFocusTrap(target);
const { generating, generate } = generator({
  allowedScore: 40,
  onGenerate: (value: string) => (state.value.password = value),
});

const onPasswordChange = (e: KeyboardEvent) => {
  setTimeout(() => {
    state.value.password = (e.target as HTMLInputElement).value.replace(
      /\s+/g,
      "",
    );
  }, 5);
};

const saveAccount = async (e: FormSubmitEvent<Schema>) => {
  loading.value = true;
  return new Promise((resolve) => {
    $api
      .post("/user", e.data)
      .then((response) => {
        emit("add", response.data.data as User);

        toast.add({
          title: "Success",
          description: response.data.message ?? "Account saved successfully",
          color: "primary",
          icon: "tabler:circle-check",
        });
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

const getRoles = () => {
  loading.value = true;
  $api
    .get("/user/roles")
    .then((response) => {
      roles.value = response.data.data as Array<Common>;
      state.value.roles = [response.data.default];
    })
    .finally(() => {
      loading.value = false;
    });
};

onMounted(() => {
  getRoles();
  nextTick(() => {
    activate();
  });
});

onBeforeUnmount(() => {
  deactivate();
});
</script>

<template>
  <TCard
    ref="target"
    :ui="{
      base: 'w-screen-95 max-w-sm relative',
      header: {
        padding: 'px-4 py-2',
      },
      body: {
        padding: 'px-4 py-2',
      },
    }"
  >
    <template #header>
      <TTypography variant="h6">Add Account</TTypography>
      <TButton
        icon="tabler:x"
        size="sm"
        variant="ghost"
        color="gray"
        @click="$emit('close')"
        :disabled="loading"
      />
    </template>
    <TForm
      :schema
      :state
      :validateOn="['submit']"
      ref="form"
      class="flex flex-auto flex-col gap-4 px-4 py-2"
      @submit="saveAccount"
    >
      <TFormGroup label="Username" name="username" required>
        <TInput v-model="state.username" :disabled="loading" />
      </TFormGroup>
      <TFormGroup label="Email" name="email">
        <TInput v-model="state.email" :disabled="loading" />
      </TFormGroup>
      <TFormGroup
        label="Password"
        name="password"
        required
        :ui="{ container: 'flex items-center gap-2 relative' }"
        :hint="strength(state.password.trim()).complexity"
      >
        <TInput
          v-model="state.password"
          @input="onPasswordChange"
          class="flex-auto"
          :disabled="loading"
        >
          <template #trailing>
            <span
              class="h-3 w-3 rounded-full"
              :style="{
                backgroundColor: strength(state.password.trim()).color,
              }"
            />
          </template>
        </TInput>
        <TButton
          icon="tabler:circle-key"
          color="gray"
          :disabled="generating || loading"
          @click="generate"
        />
      </TFormGroup>

      <TFormGroup label="Roles" name="roles" required>
        <TSelectMenu
          v-model="state.roles"
          :options="roles"
          multiple
          size="md"
          color="gray"
          placeholer="Select Role"
          value-attribute="id"
          option-attribute="name"
          :ui="{
            color: {
              gray: {
                outline: 'bg-gray-50 dark:bg-gray-700',
              },
            },
          }"
        />
      </TFormGroup>

      <div class="flex justify-end gap-4 py-2">
        <TButton type="submit" label="Save" :disabled="loading" />
        <TButton
          label="Cancel"
          color="gray"
          variant="ghost"
          @click="$emit('close')"
          :disabled="loading"
        />
      </div>
    </TForm>
    <TInnerLoading :active="loading" text="Saving account info..." />
  </TCard>
</template>
