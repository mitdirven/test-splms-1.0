<script setup lang="ts">
const props = defineProps<{
  label: string;
  description: string;
}>();

const slots = useSlots();
</script>

<template>
  <TCard class="p-5">
    <template #header>
      <div class="mb-3">
        <TTypography variant="h6" class="font-semibold">
          {{ label }}
        </TTypography>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          {{ description }}
        </p>
      </div>
    </template>
    <slot></slot>
    <template v-if="!!slots.footer" #footer>
      <slot name="footer" />
    </template>
  </TCard>
</template>
