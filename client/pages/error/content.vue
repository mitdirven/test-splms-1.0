<script setup lang="ts">
const router = useRouter();

const canGoBack = computed(() => !!router.currentRoute.value.redirectedFrom);
const goBack = () => {
  if (!canGoBack.value) {
    router.push({ name: "home" });
  } else {
    router.back();
  }
};

const props = defineProps<{
  statusCode?: string;
  icon?: string;
  title: string;
  message: string;
}>();
</script>

<template>
  <div class="-mt-64 flex flex-col items-center space-y-5">
    <div class="flex flex-col items-center gap-3">
      <TTypography variant="h1" class="text-8xl font-semibold">
        <TIcon v-if="icon" :name="icon" class="text-coral-500" />
        {{ statusCode }}
      </TTypography>
      <TTypography variant="xl" class="font-medium">{{ title }}</TTypography>
      <slot :message>
        <TTypography variant="md" class="max-w-lg text-center">
          {{ message }}
        </TTypography>
      </slot>
    </div>
    <slot name="actions">
      <TButton :label="canGoBack ? 'Go back' : 'Home'" @click="goBack" />
    </slot>
  </div>
</template>
