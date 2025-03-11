<script setup lang="ts">
import { z } from "zod";
import type { FormSubmitEvent } from "#ui/types";

const { $api } = useNuxtApp();
const { files, uploading, upload, fileBrowser } = useUploader({
  api: "/test/upload",
  checkIntegrity: true,

  sleep: {
    long: 1500,
    short: 250,
    interval: 15,
  },
});

const schema = z.object({
  files: z.instanceof(FileList).nullable(),
});

type Schema = z.output<typeof schema>;

const state = reactive({
  files: null,
});

const onSubmit = async (e: FormSubmitEvent<Schema>) => {
  return new Promise((resolve, reject) => {
    upload(3).then(resolve).catch(reject);
  });
};

watch(
  files,
  (val) => {
    // console.log(val);
  },
  { deep: true },
);
</script>

<template>
  <div class="flex flex-auto flex-col gap-4 p-4">
    <div>File Upload</div>
    <div class="flex flex-auto justify-center">
      <TForm
        :schema
        :state
        :validateOn="['submit']"
        @submit="onSubmit"
        class="flex flex-col gap-5"
      >
        <TButton
          label="Select Files"
          @click="fileBrowser('*', true).open()"
          :disabled="uploading"
        />
        <div class="flex w-screen-95 max-w-96 flex-col gap-4">
          <template v-for="file in files" :key="file.id">
            <TFileUploadItem :file="file" />
          </template>
        </div>
        <TButton label="Upload" type="submit" :disabled="uploading" />
      </TForm>
    </div>
    <div></div>
  </div>
</template>
