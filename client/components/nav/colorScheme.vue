<script setup lang="ts">
import { primaries, grays } from "@/config/tw";
import colors from "#tailwind-config/theme/colors";

const colorMode = useColorMode();
const appConfig = useAppConfig();

const props = withDefaults(
  defineProps<{
    customOnly?: boolean;
  }>(),
  {
    customOnly: true,
  },
);

const primaryColors = computed(() => {
  let c = props.customOnly ? Object.keys(primaries) : appConfig.ui.colors;
  return c
    .filter((color) => color !== "primary")
    .filter((color) => Object.keys(grays).indexOf(color) === -1)
    .map((color) => ({
      value: color,
      text: color,
      hex: colors[color as keyof typeof colors][
        colorMode.value === "dark" ? 400 : 500
      ],
    }));
});

const grayColors = computed(() => {
  let c = props.customOnly
    ? Object.keys(grays)
    : ["slate", "cool", "zinc", "neutral", "stone"].concat(Object.keys(grays));
  return c.map((color) => ({
    value: color,
    text: color,
    hex: colors[color as keyof typeof colors][
      colorMode.value === "dark" ? 400 : 500
    ],
  }));
});
</script>

<template>
  <TPopover
    mode="hover"
    :popper="{ strategy: 'absolute' }"
    :ui="{ width: 'w-[156px]' }"
  >
    <TButton
      color="gray"
      variant="ghost"
      size="md"
      square
      icon="tabler:color-swatch"
      :ui="{ rounded: 'rounded-full', icon: { size: { md: 'h-6 w-6' } } }"
    />

    <template #panel="{ close }">
      <div class="p-1.5">
        <div class="grid grid-cols-5 gap-px">
          <template
            v-for="primary in primaryColors"
            :key="`primary_${primary}`"
          >
            <ColorPill
              :hex="primary.hex"
              :name="primary.value"
              :text="primary.text"
              :active="appConfig.ui.primary"
              @select="appConfig.ui.primary = primary.value"
            />
          </template>
        </div>
        <hr class="my-2 border-gray-200 dark:border-gray-800" />
        <div class="grid grid-cols-5 gap-px">
          <template v-for="gray in grayColors" :key="`gray_${gray}`">
            <ColorPill
              :hex="gray.hex"
              :name="gray.value"
              :text="gray.text"
              :active="appConfig.ui.gray"
              @select="appConfig.ui.gray = gray.value"
            />
          </template>
        </div>
      </div>
    </template>
  </TPopover>
</template>
