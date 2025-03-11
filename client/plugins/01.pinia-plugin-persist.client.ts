import { createPersistedStatePlugin } from "pinia-plugin-persistedstate-2";
import { EncryptStorage } from "encrypt-storage";
import type { Pinia } from "pinia";
import { PiniaSharedState } from "pinia-shared-state";

export default defineNuxtPlugin((nuxtApp) => {
  const runtimeCongfig = useRuntimeConfig();
  const { store } = runtimeCongfig.public;
  const encrypt =
    store.encrypt === "true" ||
    (store.encrypt === "auto" && import.meta.env.PROD);

  const pinia: Pinia = nuxtApp.$pinia as Pinia;
  let persistOptions = {};

  if (encrypt && !!pinia) {
    const salt = !!store.salt ? store.salt : "nosi-ba-lasi";
    const prefix = !!store.prefix
      ? store.prefix
      : "fa0feac2dce13c9e6d13d0baf54e364a";
    const ls = new EncryptStorage(salt, {
      prefix: prefix,
      stateManagementUse: true,
    });

    persistOptions = {
      storage: {
        getItem: (key: string) => {
          let data = ls.getItem(ls.hash(key))?.replace(/^\s+|\s+$/gm, "") ?? "";
          if (data == "") return "[]";
          return data;
        },
        setItem: (key: string, value: any) => ls.setItem(ls.hash(key), value),
        removeItem: (key: any) => ls.removeItem(ls.hash(key)),
      },
    };
  }
  pinia.use(createPersistedStatePlugin(persistOptions));
  pinia.use(PiniaSharedState({ type: "localstorage" }));
});
