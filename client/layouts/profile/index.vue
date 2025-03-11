<script setup lang="ts">
import aMenus from "../default/config/avatarOptions";
const $route = useRoute();
const $system = useSystemStore();
</script>

<template>
  <Layout>
    <LayoutBody>
      <TopNav fixed :avatarOptions="aMenus">
        <template #prepend><span /></template>
      </TopNav>
      <slot />
    </LayoutBody>
  </Layout>
</template>
