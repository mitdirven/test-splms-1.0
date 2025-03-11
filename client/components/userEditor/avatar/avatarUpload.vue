<script setup lang="ts">
import { useFocusTrap } from "@vueuse/integrations/useFocusTrap";
import type { User } from "~/types/models/users";
import type { FileItem } from "~/types/composables/useUploader";

const props = defineProps<{
  api: string;
}>();
const emit = defineEmits(["close"]);

const user = defineModel("user", {
  type: Object as PropType<User | Omit<User, "id"> | null>,
  default: null,
});

const { msToReadable, formatSize } = useUtils();
const { files, uploading, upload, fileBrowser } = useUploader({
  api: props.api,
});
const toast = useToast();

const loading = ref(false);
const target = ref();

const file = computed<FileItem>(() => files.value[0] as FileItem);
const image = computed(() => fileToURL(file.value?.file));

const { hasFocus, activate, deactivate } = useFocusTrap(target);

const uploadImage = async () => {
  try {
    loading.value = true;
    const responses = await upload();
    const response = responses?.[0];

    const data = response?.data.data;
    if (data) {
      user.value = data as User;

      toast.add({
        title: "Profile Updated",
        description: response.data.message ?? "Image saved successfully",
        color: "primary",
        icon: "tabler:circle-check",
      });
    }
  } catch (error) {
  } finally {
    loading.value = false;
  }
};

const fileToURL = (file?: File) => {
  if (file) {
    return URL.createObjectURL(file);
  }
  return undefined;
};

onMounted(() => {
  nextTick(() => {
    activate();
  });
});

onBeforeUnmount(() => {
  deactivate();
});
</script>

<style scoped lang="scss">
.progress-ring__circle {
  transition: stroke-dashoffset 0.35s;
}
</style>

<template>
  <TCard
    ref="target"
    :ui="{
      base: 'sm:w-screen-95 max-w-sm',
      divide: 'divide-y divide-gray-400/25',
      header: { base: 'flex items-center gap-2', padding: 'px-3 py-1' },
      body: { base: 'flex flex-col gap-5', padding: 'px-3 py-2' },
      footer: {
        base: 'justify-end flex items-center gap-2',
        padding: 'px-3 py-2',
      },
    }"
  >
    <template #header>
      <TTypography variant="h6" class="flex-auto"> Image Upload </TTypography>
      <TButton
        icon="tabler:x"
        size="xs"
        variant="ghost"
        color="gray"
        :padded="false"
        :ui="{ rounded: 'rounded-full' }"
        @click="emit('close')"
      />
    </template>
    <div class="flex flex-col items-center gap-2">
      <div class="flex w-full items-center justify-center p-2">
        <div class="relative flex h-40 w-40 items-center justify-center p-2">
          <svg
            class="pointer-events-none absolute h-full w-full"
            viewBox="0 0 100 100"
          >
            <!-- Background circle -->
            <circle
              class="stroke-current text-gray-100 dark:text-gray-700"
              stroke-width="4"
              cx="50"
              cy="50"
              r="48"
              fill="transparent"
            />
            <!-- Progress circle -->
            <circle
              class="progress-ring__circle text-primary-500 origin-[50%_50%] -rotate-90 stroke-current"
              stroke-width="4"
              stroke-linecap="round"
              cx="50"
              cy="50"
              r="48"
              fill="transparent"
              stroke-dasharray="301.59"
              :stroke-dashoffset="`calc(301.59px - (301.59px * ${file?.upload.progress || 0}) / 100)`"
            />
          </svg>
          <div
            class="flex aspect-square w-full items-center justify-center overflow-hidden rounded-full"
          >
            <img
              v-if="image"
              :src="image"
              alt="Avatar"
              class="relative h-full w-full"
            />
            <TIcon
              v-else
              name="tabler:user-filled"
              class="relative h-24 w-24 text-gray-400 sm:h-32 sm:w-32"
            />
          </div>
        </div>
      </div>
    </div>
    <template #footer>
      <div class="flex-auto">
        <TButton
          v-if="!uploading"
          label="Select Image"
          variant="outline"
          color="gray"
          @click="fileBrowser('image/*')?.open()"
        />
        <TTypography v-if="uploading" variant="sm">
          {{ `${formatSize(file.upload.speed)}/s` }}
        </TTypography>
      </div>
      <div class="flex items-center gap-2">
        <TButton
          label="Upload"
          size="sm"
          :disabled="!image"
          :loading="loading"
          @click="uploadImage()"
        />
        <TButton
          label="Cancel"
          size="sm"
          color="gray"
          variant="ghost"
          @click="emit('close')"
        />
      </div>
    </template>
  </TCard>
</template>
