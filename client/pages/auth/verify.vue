<script setup lang="ts">
import EmailEditor from "~/components/userEditor/account/email.vue";
import type { User } from "~/types/models";

const auth = useAuthStore();
const { $router, $api } = useNuxtApp();
const $route = useRoute();
const toast = useToast();

const loading = ref(false);
const error = ref<string>();

const verify = () => {
  loading.value = true;
  const id = $route.params.id;
  const hash = $route.params.hash;
  const expires = $route.query.expires;
  const signature = $route.query.signature;

  $api
    .get(`/auth/email/verify/${id}/${hash}`, {
      params: { expires, signature },
    })
    .then((response) => {
      auth.setUser(response.data.data as User);
      toast.add({
        title: "Email verified",
        description: response.data.message,
        color: "green",
        icon: "tabler:check",
      });
    })
    .catch((e) => {
      error.value = e.response.data?.message ?? "Failed to verify email";
      log(error.value);
    })
    .finally(() => {
      loading.value = false;
    });
};

// watch(
//   () => auth.verified,
//   () => {
//     if (auth.verified) {
//       $router.push(($route.query.redirect as string) || { name: "home" });
//     }
//   },
//   { immediate: true },
// );

onMounted(() => {
  verify();
});
</script>

<template>
  <TCard
    :ui="{
      base: 'w-screen-95 max-w-md px-8 relative',
      divide: 'divide-y divide-gray-400/25',
      header: {
        base: 'flex items-center flex-col gap-5',
        padding: 'px-3 pt-10 pb-5',
      },
      body: {
        base: 'text-center flex flex-col items-center gap-5',
        padding: 'px-3 py-5',
      },
    }"
  >
    <template #header>
      <div
        class="flex items-center justify-center rounded-full bg-gray-50 p-2 ring-4 dark:bg-gray-800"
        :class="{
          'ring-sunset-500': !!error,
          'ring-gray-200 dark:ring-gray-600': loading,
          'ring-pine-400 dark:ring-pine-600': !loading && !error,
        }"
      >
        <TIcon
          :name="
            !!error
              ? 'tabler:mail-exclamation'
              : loading
                ? 'tabler:loader-2'
                : 'tabler:mail-check'
          "
          class="h-16 w-16"
          :class="{
            'animate-spin': loading,
            'text-sunset-500': error,
            'text-pine-400 dark:text-pine-600': !loading && !error,
          }"
        />
      </div>
      <TTypography v-if="loading" class="font-semibold">
        Verifying your email
      </TTypography>
      <template v-else>
        <TTypography class="font-semibold">
          {{ error || "Email verified, you can safely close this window" }}
        </TTypography>
      </template>
    </template>
  </TCard>
</template>
