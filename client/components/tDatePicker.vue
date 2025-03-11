<script setup lang="ts">
import type { DarkModeClassConfig } from "vue-screen-utils";
import { DatePicker as VCalendarDatePicker } from "v-calendar";

const props = defineProps({
  transparent: {
    type: Boolean,
    default: true,
  },
  borderless: {
    type: Boolean,
    default: true,
  },
  isDark: {
    type: [Boolean, String, Object] as PropType<
      DarkModeClassConfig | boolean | "system"
    >,
    default: { selector: "html", darkClass: "dark" },
  },
  color: {
    type: String,
    default: "primary",
  },
  timezone: {
    type: String,
    default: "Asia/Manila",
  },
});

const emit = defineEmits(["close"]);
</script>

<template>
  <VCalendarDatePicker
    :transparent
    :borderless
    :isDark
    :color
    :timezone
    @update:modelValue="emit('close')"
    @dayclick="
      (day, event) => {
        event.target.blur();
      }
    "
  />
</template>
