<script setup lang="ts">
defineProps<{
  name: string;
  text: string;
  hex: string;
  active: string;
}>();

const emit = defineEmits(["select"]);
</script>

<template>
  <TTooltip :text="text" class="capitalize" :openDelay="500">
    <TButton
      color="white"
      square
      :ui="{
        color: {
          white: {
            solid:
              'ring-0 bg-gray-100 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800',
            ghost: 'hover:bg-gray-50 dark:hover:bg-gray-800/50',
          },
        },
      }"
      :variant="name === active ? 'solid' : 'ghost'"
      @click.stop.prevent="$emit('select')"
    >
      <span
        class="inline-block h-3 w-3 rounded-full"
        :style="{ backgroundColor: hex }"
      />
    </TButton>
  </TTooltip>
</template>
