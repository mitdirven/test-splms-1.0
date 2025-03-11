<script setup lang="ts">
import type { AvatarOptions } from "~/types";

const props = withDefaults(
  defineProps<{
    fixed?: boolean;
    colorToggle?: boolean;
    themeToggle?: boolean;
    screenToggle?: boolean;
    avatarOptions?: Array<AvatarOptions>;
    pwaInstall?: boolean;
  }>(),
  {
    fixed: false,
    colorToggle: true,
    themeToggle: true,
    screenToggle: true,
    pwaInstall: true,
  },
);
const emit = defineEmits(["toggleSidebar"]);
const $system = useSystemStore();

const hidden = ref(false);

onMounted(() => {
  $system.sidebar.collapsed = false;
});
</script>

<template>
  <nav
    class="layout-border--b z-30 flex w-full items-center bg-gray-50 transition-all dark:bg-gray-700"
    :class="{
      'sticky top-0': fixed && !hidden,
      '-top-[73px]': hidden,
    }"
  >
    <div
      class="relative flex flex-auto items-center justify-between gap-1.5 px-6 py-3"
    >
      <slot name="prepend">
        <div class="">
          <TButton
            color="gray"
            variant="ghost"
            size="md"
            square
            icon="tabler:menu-2"
            :ui="{ rounded: 'rounded-full', icon: { size: { md: 'h-6 w-6' } } }"
            @click="$system.sidebar.collapsed = !$system.sidebar.collapsed"
          />
        </div>
      </slot>
      <div class="flex flex-auto items-center gap-1.5">
        <slot></slot>
      </div>

      <slot name="append">
        <div class="flex items-center gap-1.5">
          <PwaInstall v-if="pwaInstall" />
          <ColorScheme v-if="colorToggle" />
          <ScreenToggle v-if="screenToggle" />
          <Theme v-if="themeToggle" />
          <Avatar :menus="avatarOptions" />
        </div>
      </slot>
      <div
        v-if="false"
        class="absolute left-1/2 top-full flex -translate-x-1/2 items-center justify-center transition-all"
        :class="{
          '-translate-y-1/2': !hidden,
        }"
      >
        <TButton
          icon="tabler:square-rounded-chevron-up-filled"
          variant="ghost"
          color="gray"
          :padded="false"
          :ui="{
            roudned: 'rounded-full',
            icon: {
              base: `transition-transform ${hidden ? 'rotate-180' : ''}`,
            },
          }"
          @click="hidden = !hidden"
        />
      </div>
    </div>
  </nav>
</template>
