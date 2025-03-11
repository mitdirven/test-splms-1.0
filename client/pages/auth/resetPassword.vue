<script setup lang="ts">
import { z } from "zod";
import { type AxiosResponse } from "axios";
import type { FormSubmitEvent } from "#ui/types";

const $route = useRoute();
const { $api, $router } = useNuxtApp();

const { passwordRules } = usePassword();

const schema = z
  .object({
    email: z
      .string({ message: "Email address is required!" })
      .email("Must be a valid email"),
    token: z.string({ message: "Token is required" }),
    password: passwordRules(),
    password_confirmation: passwordRules(),
  })
  .refine(
    ({ password, password_confirmation }) => password === password_confirmation,
    {
      message: "Passwords do not match",
      path: ["password_confirmation"],
    },
  );

type Schema = z.output<typeof schema>;

const state = reactive({
  email: ($route.query.email as string) ?? null,
  password: null,
  password_confirmation: null,
  token: $route.params.token as string,
});

const loading = ref<boolean>(false);
const loadingMessage = ref("");
const changed = ref<boolean>(false);
const timer = ref<NodeJS.Timeout | null>(null);
const time = ref(5);
const passwordCanged = (response: AxiosResponse) => {
  const toast = useToast();
  toast.add({
    title: "Password reset successful!",
    description: response.data.message,
    color: "green",
    icon: "tabler:check",
  });

  changed.value = true;
  timer.value = setInterval(() => {
    if (time.value > 0) {
      time.value--;
    } else {
      clearInterval(timer.value!);
      $router.push({ name: "login" });
    }
  }, 1000);
};

const resetPassword = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    loading.value = true;
    loadingMessage.value = "Sending email...";

    $api
      .post("/password/reset", {
        email: e.data.email,
        token: e.data.token,
        password: e.data.password,
        password_confirmation: e.data.password_confirmation,
      })
      .then(passwordCanged)
      .finally(() => {
        loading.value = false;
      });
  });
};
</script>

<template>
  <TCard
    class="w-screen-95 max-w-md select-none shadow-lg"
    :ui="{
      body: {
        base: 'flex flex-col gap-5 ',
        padding: 'sm:px-8 sm:py-12',
      },
    }"
  >
    <div class="flex flex-col items-center gap-4">
      <div class="relative aspect-square h-20 w-20">
        <img
          src="/favicons/baguioseal.svg"
          class="absolute inset-0"
          :class="{ 'animate-ping': loading }"
        />
        <img src="/favicons/baguioseal.svg" class="absolute inset-0" />
      </div>
      <span class="text-2xl">{{ $config.public.product_name }}</span>
    </div>

    <TForm
      v-if="!changed"
      :schema="schema"
      :state="state"
      :validateOn="['submit']"
      class="flex flex-col gap-5"
      @submit="resetPassword"
    >
      <p>Enter your email and your new password to reset your password.</p>
      <TInput name="token" v-model="state.token" type="hidden" />
      <TFormGroup name="email" label="Email" required>
        <TInput
          v-model="state.email"
          placeholder="example@email.com"
          color="gray"
          size="md"
          :disabled="loading"
        />
      </TFormGroup>
      <TFormGroup name="password" label="Password" required>
        <TInput
          v-model="state.password"
          type="password"
          color="gray"
          size="md"
          :disabled="loading"
        />
      </TFormGroup>
      <TFormGroup
        name="password_confirmation"
        label="Confirm Password"
        required
      >
        <TInput
          v-model="state.password_confirmation"
          type="password"
          color="gray"
          size="md"
          :disabled="loading"
        />
      </TFormGroup>

      <TButton label="Reset Password" block type="submit" :loading="loading" />

      <div class="flex items-center justify-center gap-2">
        <TButton
          variant="link"
          icon="tabler:chevron-left"
          :disabled="loading"
          :to="{ name: 'login' }"
        >
          Go to login
        </TButton>
      </div>
    </TForm>
    <div v-else>
      <p>
        Your password has been reset. You will be redirected in
        {{ time }} seconds.
      </p>
    </div>
  </TCard>
</template>
