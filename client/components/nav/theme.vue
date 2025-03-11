<script setup lang="ts">
const colorModes = ref([
  {
    label: "Light",
    icon: "tabler:sun",
    value: "light",
  },
  {
    label: "Dark",
    icon: "tabler:moon",
    value: "dark",
  },
  {
    label: "System",
    icon: "tabler:device-desktop",
    value: "system",
  },
]);
</script>
<template>
  <TPopover>
    <TButton
      color="gray"
      variant="ghost"
      size="md"
      square
      :icon="
        colorModes.find(({ value }) => value == $colorMode.preference)?.icon
      "
      :ui="{ rounded: 'rounded-full', icon: { size: { md: 'h-6 w-6' } } }"
    />
    <template #panel="{ close }">
      <div class="flex w-screen-95 max-w-32 flex-col gap-1.5 p-1.5">
        <TButton
          v-for="{ label, icon, value } in colorModes"
          variant="ghost"
          :color="$colorMode.preference == value ? 'primary' : 'gray'"
          :label
          :icon
          @click="[($colorMode.preference = value), close()]"
        />
      </div>
    </template>
  </TPopover>
</template>
