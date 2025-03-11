<script setup lang="ts">
import type { FileItem } from "~/types/composables/useUploader";

const props = defineProps<{
  file: FileItem;
}>();

const { msToReadable, formatSize } = useUtils();

const uploading = computed(() => props.file.status === "uploading");

const state = computed(() => {
  if (["pending", "uploading"].indexOf(props.file.status) > -1) {
    return null;
  }
  if (props.file.status === "error") {
    return {
      icon: "tabler:alert-triangle",
      message: "File Upload failed!",
      class: "text-sunset-400",
    };
  }

  if (props.file.hashing) {
    return {
      icon: "tabler:loader-2",
      message: "Checking file integrity...",
      class: "animate-spin",
    };
  }
  if (props.file.matchedHash === false) {
    return {
      icon: "tabler:alert-triangle",
      message: "File Upload sucessful with mismatched hash!",
      class: "text-sunflower-400",
    };
  }
  if (props.file.matchedHash === true || props.file.status == "complete") {
    return {
      icon: "tabler:check",
      message: "File Upload sucessful!",
      class: "text-pine-400",
    };
  }
});
</script>

<template>
  <div class="flex flex-col rounded-lg border border-gray-400/25 px-3 py-1">
    <div class="flex items-center justify-between gap-2">
      <div class="line-clamp-1 flex-auto">
        <input
          v-model="file.name"
          class="w-full bg-inherit text-sm outline-none"
        />
      </div>
      <TIcon v-if="uploading" name="line-md:uploading-loop" />
      <TButton
        v-else
        icon="tabler:x"
        variant="ghost"
        color="gray"
        size="xs"
        @click="file.remove(true)"
        :padded="false"
        :ui="{ rounded: 'rounded-full' }"
      />
    </div>

    <div class="flex items-center justify-between gap-2 text-sm">
      <span>{{ formatSize(file.file.size) }}</span>
      <span v-if="file.status === 'uploading' && file.upload.progress < 100">
        {{ `${formatSize(file.upload.speed)}/s` }}
      </span>
    </div>

    <div class="flex items-center gap-2">
      <TProgress :value="file.upload.progress" />
      <TTooltip v-if="!!state" :text="state.message">
        <TIcon :name="state.icon" class="h-4 w-4" :class="state.class" />
      </TTooltip>
    </div>
    <table v-if="false">
      <tbody class="[&>tr>td]:p-2">
        <tr>
          <td>speed</td>
          <td>
            {{ `${formatSize(file.upload.speed)}/s` }}
          </td>
        </tr>
        <tr>
          <td>ellapsed</td>
          <td>
            {{ `${msToReadable(file.upload.ellapsed).str}` }}
          </td>
        </tr>
        <tr>
          <td>loaded</td>
          <td>
            {{ `${formatSize(file.upload.loaded)}` }}
          </td>
        </tr>
        <tr>
          <td>timeRemaining</td>
          <td>
            {{ `${msToReadable(file.upload.timeRemaining).str}` }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
