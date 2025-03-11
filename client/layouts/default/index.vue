<script setup lang="ts">
import type { BreadcrumbLink } from "#ui/types";
import menus from "./config/sidebar";
import aMenus from "./config/avatarOptions";

const $route = useRoute();

const $system = useSystemStore();

const crumbs = computed<Array<BreadcrumbLink>>(() =>
  $route.matched
    .filter((r) => !!r.name)
    .map(
      (r): BreadcrumbLink => ({
        label: (r.meta.title as string) ?? r.name,
        to: r,
        icon: (r.meta.icon as string) ?? null,
      }),
    ),
);
</script>

<template>
  <Layout>
    <Sidebar v-model:expand="$system.sidebar.collapsed" :menus />
    <LayoutBody class="ml-[4.5rem] transition-all">
      <TopNav fixed :avatarOptions="aMenus">
        <TButton
          v-if="!!$route.matched.find((r) => r.name === 'settings')"
          label="Home"
          icon="tabler:home"
          color="gray"
          variant="ghost"
          :to="{ name: 'home' }"
        />
      </TopNav>
      <TPageTitle
        v-if="false"
        :title="($route.meta.title as string) ?? null"
        :icon="($route.meta.icon as string) ?? null"
        :breadcrumbs="crumbs"
      />
      <slot />
    </LayoutBody>
  </Layout>
</template>
