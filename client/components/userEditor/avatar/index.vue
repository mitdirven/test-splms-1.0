<script setup lang="ts">
import type { ProfileImage, User } from "~/types/models/users";
import AvatarUpload from "./avatarUpload.vue";
import Overlay from "../utils/overlay.vue";
import Delete from "./delete.vue";
const props = defineProps<{
  api: {
    create: string;
    update: string;
    delete: string;
  };
}>();

const user = defineModel("user", {
  type: Object as PropType<User | Omit<User, "id"> | null>,
  default: null,
});

const { $api } = useNuxtApp();
const toast = useToast();

const loading = ref(false);
const modal = ref<{
  show: boolean;
  type: string;
  data?: ProfileImage;
}>({
  show: false,
  type: "upload",
  data: undefined,
});

const image = computed(() =>
  user.value?.profile?.images?.find((i: ProfileImage) => i.primary),
);

const getOptions = (img: ProfileImage) => {
  return [
    [
      {
        label: "Use Image",
        icon: "tabler:user-hexagon",
        click: () => changeAvatar(img),
      },
      {
        label: "Delete",
        icon: "tabler:trash",
        click: () => openModal(img, "delete"),
      },
    ],
  ];
};

const changeAvatar = (img: ProfileImage) => {
  if (img.primary) {
    return;
  }

  loading.value = true;
  $api
    .patch(`${props.api.update}/${img.id}`)
    .then((response) => {
      user.value = response.data.data as User;

      toast.add({
        title: "Profile Updated",
        description: response.data.message ?? "Image saved successfully",
        color: "primary",
        icon: "tabler:circle-check",
      });
    })
    .finally(() => {
      loading.value = false;
    });
};

const openModal = (data?: ProfileImage, type: string = "upload") => {
  modal.value.data = data;
  modal.value.type = type;
  modal.value.show = true;
};
</script>

<template>
  <div
    class="before:bg-primary-500/25 relative flex h-full flex-col overflow-hidden border border-gray-400 p-4 before:absolute before:bottom-[80%] before:left-1/2 before:aspect-square before:w-[300%] before:-translate-x-1/2 before:rounded-full before:content-[''] dark:bg-gray-700"
  >
    <div class="flex items-center justify-center pt-5">
      <div
        class="relative flex aspect-square w-32 items-center justify-center overflow-hidden rounded-full border border-gray-400 bg-gray-100 ring-8 ring-gray-100 dark:bg-gray-800 dark:ring-gray-600"
      >
        <template v-if="!loading">
          <img
            v-if="image"
            :src="image.url.lg"
            :alt="image.alt"
            class="h-full w-full"
          />
          <TIcon
            v-else
            name="tabler:user-filled"
            class="h-20 w-20 text-gray-400"
          />
        </template>
        <TIcon name="tabler:loader-2" v-else class="h-20 w-20 animate-spin" />
      </div>
    </div>
    <TTypography class="py-2 text-center text-gray-400">
      Choose profile image
    </TTypography>
    <div class="flex-auto overflow-auto p-2">
      <div class="flex flex-wrap items-start justify-center gap-2">
        <template v-for="img in user?.profile?.images ?? []" :key="img.id">
          <TDropdown
            :items="getOptions(img)"
            :popper="{ placement: 'bottom-start', arrow: true }"
            :ui="{
              background: 'dark:bg-gray-600',
              arrow: {
                background: 'dark:before:bg-gray-600',
              },
              trigger: img.primary ? '!cursor-not-allowed' : '',
            }"
            :disabled="img.primary"
          >
            <TAvatar
              size="xl"
              :src="img.url.sm"
              :alt="`${img.alt}_thumb`"
              :ui="{ wrapper: 'border border-gray-400' }"
              :chipColor="img.primary ? 'primary' : undefined"
            />
          </TDropdown>
        </template>
      </div>
    </div>
    <TDivider
      label="OR"
      :ui="{
        border: {
          base: 'dark:border-gray-500',
        },
        label: 'text-primary-600 dark:text-primary-400',
      }"
    />
    <div class="flex items-center justify-center gap-2">
      <TTypography variant="sm">
        Select profile picture from your device
      </TTypography>
      <TButton
        label="Add Image"
        icon="tabler:upload"
        variant="outline"
        size="xs"
        :ui="{ rounded: 'rounded-full' }"
        @click="openModal(undefined, 'upload')"
      />
    </div>
    <Overlay v-model="modal.show" v-slot="{ close }">
      <AvatarUpload
        v-if="modal.type === 'upload'"
        :api="api.create"
        v-model:user="user"
        @update:user="close"
        @close="close"
      />
      <Delete
        v-else-if="modal.type === 'delete'"
        :api="api.delete"
        :image="modal.data!"
        v-model:user="user"
        @update:user="close"
        @close="close"
      />
    </Overlay>
  </div>
</template>
