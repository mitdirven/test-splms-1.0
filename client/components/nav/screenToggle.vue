<script setup lang="ts">
import { useFullscreen } from "@vueuse/core";
const { isFullscreen, enter, exit, toggle } = useFullscreen();
</script>
<template>
  <TButton
    color="gray"
    variant="ghost"
    size="md"
    square
    :icon="isFullscreen ? 'tabler:minimize' : 'tabler:maximize'"
    :ui="{ rounded: 'rounded-full', icon: { size: { md: 'h-6 w-6' } } }"
    @click="toggle"
  />
</template>
