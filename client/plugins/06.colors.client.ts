import { useStorage } from "@vueuse/core";

export default defineNuxtPlugin((nuxtApp) => {
  const config = useAppConfig().ui;
  const primary = useStorage("primary", config.primary);
  const gray = useStorage("gray", config.gray);
  const colorMode = useColorMode();

  config.primary = primary.value;
  config.gray = gray.value;

  watch(config, (val) => {
    primary.value = val.primary;
    gray.value = val.gray;
  });

  window.addEventListener("storage", (e) => {
    if (e.key === "primary") {
      primary.value = e.newValue;
      config.primary = e.newValue ?? "pine";
    }
    if (e.key === "gray") {
      gray.value = e.newValue;
      config.gray = e.newValue ?? "steel";
    }
    if (e.key === "nuxt-color-mode") {
      colorMode.preference = e.newValue ?? "system";
    }
  });

  return { provide: { primary, gray } };
});
