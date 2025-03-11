<script setup lang="ts">
import type { MenuOption } from "~/types";
import type { RouteLocationRaw } from "vue-router";

const $router = useRouter();
const $route = useRoute();

const SBHeader = defineAsyncComponent(() => import("./sb/header.vue"));
const SBActionButton = defineAsyncComponent(
  () => import("./sb/actionButton.vue"),
);

const props = defineProps<{
  expand: boolean;
  menus: Array<MenuOption>;
}>();
const emit = defineEmits(["update:expand"]);

const _menus = computed(() => filterMenu(props.menus));

const filterMenu = (menu: MenuOption[]) => {
  let result: MenuOption[] = [];
  menu.forEach((item) => {
    if (toValue(item.hidden) !== true) {
      let children = null;
      if (!!item.children && item.children.length > 0) {
        children = { children: filterMenu(item.children) };
      }
      result.push(Object.assign({}, item, children));
    }
  });
  return checkActive(result);
};

const checkActive = (menu: MenuOption[]) => {
  let tmp = [...menu];
  tmp.forEach((item) => {
    item.active = isActive(item);
    if (item.children) checkActive(item.children);
  });
  return tmp;
};

const isActive = (item: MenuOption): boolean => {
  let exact = false;
  if (!!item.to) {
    let r = $router.resolve(item.to as RouteLocationRaw);
    exact = r.name === $route.name;
  }
  return exact || (item.children?.some(isActive) ?? false);
};
</script>

<template>
  <div
    class="fixed inset-0 z-50 bg-gray-800 transition-all dark:bg-gray-100"
    :class="{
      '!bg-opacity-15': expand,
      'pointer-events-none !bg-opacity-0': !expand,
    }"
    @click="$emit('update:expand', false)"
  >
    <aside
      class="layout-border--r pointer-events-auto relative flex h-screen flex-col gap-[0.625rem] bg-gray-50 transition-all dark:bg-gray-800"
      :class="{
        'w-72': expand,
        'w-[4.5rem]': !expand,
      }"
      @click.prevent.stop
    >
      <SBHeader
        :expand
        :title="$config.public.product_name"
        @close="$emit('update:expand', false)"
      />
      <div
        class="flex flex-col gap-2 px-4 py-3"
        :class="{
          'overflow-y-auto': expand,
        }"
      >
        <template v-for="menu in _menus" :key="`_${menu.label}_`">
          <SBActionButton :expand :menu />
        </template>
      </div>
      <div
        class="flex flex-col gap-2 px-4 py-3"
        :class="{
          'overflow-y-auto': expand,
        }"
      >
        <template>
          
        </template>
      </div>
    </aside>
  </div>
</template>
