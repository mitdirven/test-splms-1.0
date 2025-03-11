import { type AxiosResponse } from "axios";
import defu from "defu";
import type { UploadOptions, FileItem } from "~/types/composables/useUploader";

export const useUploader = (
  options?: UploadOptions | ComputedRef<UploadOptions>,
) => {
  const { uniqid } = useUtils();
  const { $api } = useNuxtApp();

  const defaults = {
    multiple: false,
    sleep: {
      long: 1500,
      short: 250,
      interval: 15,
    },
  };

  const files = ref<Array<FileItem>>([]);

  const _options = computed<UploadOptions>(() =>
    defu(toValue(options), defaults),
  );
  const uploading = computed(() =>
    files.value.some((file) => file.status === "uploading"),
  );

  const fileBrowser = (accept: string = "*") => {
    if (!uploading.value) {
      const { openFileBrowser } = useFileBrowser({
        accept,
        multiple: _options.value.multiple,
        onChange: (e: Event) => {
          if (!!e.target) {
            addFile((e.target as HTMLInputElement).files!);
          }
        },
      });

      return {
        open: openFileBrowser,
      };
    }
  };

  const addFile = (
    file: File | Array<File> | FileList,
    unique: boolean = false,
  ): Array<string> => {
    if (!uploading.value) {
      if (!_options.value.multiple) {
        files.value = [];
      }
      if (file instanceof File) {
        files.value.push(_createUploadItem(file));
      } else if (_iterable(file)) {
        const fs = Array.from(file);
        if (_options.value.multiple) {
          fs.forEach((f: File) => {
            if (!unique || (unique && !_fileExists(f))) {
              files.value.push(_createUploadItem(f));
            }
          });
        } else {
          files.value.push(_createUploadItem(fs[0]));
        }
      }
    }
    return files.value.map((file) => file.id);
  };

  const removeFile = (id: string, force: boolean = false) => {
    if (!uploading.value) {
      files.value = files.value.filter((file, index) => {
        if (file.status == "uploading") {
          return !force;
        }
        return file.id !== id;
      });
    }
    return files.value.map((file) => file.id);
  };

  const upload = async (concurrence: number = 1) => {
    if (!uploading.value) {
      const valid = files.value.filter(
        (file: FileItem) => ["pending", "cancelled"].indexOf(file.status) > -1,
      );
      valid.forEach((file: FileItem) => {
        file.status = "uploading";
      });
      const results = [];
      const executing = new Set();

      for (const file of files.value) {
        const uploadPromise = uploadFile(file).then((result) => {
          executing.delete(uploadPromise);
          return result;
        });

        results.push(uploadPromise);
        executing.add(uploadPromise);

        if (executing.size >= concurrence) {
          await Promise.race(executing);
        }
      }

      await Promise.all(executing);
      return Promise.all(results);
    }
  };

  const uploadFile = async (item: FileItem): Promise<AxiosResponse> => {
    item.status = "uploading";
    let result = null;

    _upRate(item);

    while (
      item.status === "uploading" &&
      !!files.value.find((f) => f.id === item.id)
    ) {
      try {
        const start = Date.now();
        await _sleeper(_options.value.sleep?.short ?? defaults.sleep.short);
        const interval =
          _options.value.sleep?.interval ?? defaults.sleep.interval;
        const formData = _createFormData(item);

        result = await $api.post(_options.value.api, formData);

        const file = formData.get("file") as File;
        if (!!file) {
          item.upload.loaded += file.size;
        }

        if (result.data.part % interval == 0) {
          await _sleeper(_options.value.sleep?.long ?? defaults.sleep.long);
        }

        if (result.status === 200 || result.status === 201) {
          item.state = {
            uid: result.data.uid ?? item.state?.uid,
            part: result.data.part,
            parts: result.data.parts ?? item.state?.parts,
            length: result.data.length,
            progress: result.data.progress,
          };
          item.status = result.data.status;
        }
      } catch (e) {
        item.status = "error";
      }
    }

    item.upload.speed = 0;
    clearTimeout(item.upload.timer!);
    item.upload.progress = 100;
    if (item.status === "complete") {
      if (_options.value.checkIntegrity) {
        item.hashing = true;
        const hash = await getHash("SHA-256", item.file);
        item.matchedHash = hash == result?.data.sha256;
        item.hashing = false;
      }
    }

    return result!;
  };

  const getHash = async (algorithm: string, data: any) => {
    const main = async (msgUint8: BufferSource) => {
      const hashBuffer = await crypto.subtle.digest(algorithm, msgUint8);
      const hashArray = Array.from(new Uint8Array(hashBuffer));
      return hashArray.map((b) => b.toString(16).padStart(2, "0")).join("");
    };

    if (data instanceof Blob) {
      const arrayBuffer = await data.arrayBuffer();
      const msgUint8 = new Uint8Array(arrayBuffer);
      return await main(msgUint8);
    }
    const encoder = new TextEncoder();
    const msgUint8 = encoder.encode(data);
    return await main(msgUint8);
  };

  const _upRate = (item: FileItem) => {
    if (item.status == "uploading") {
      item.upload.start = Date.now();
      item.upload.timer = setInterval(async () => {
        item.upload.ellapsed = Date.now() - item.upload.start;
        item.upload.speed = item.upload.loaded / (item.upload.ellapsed / 1000);
        const remaining =
          ((item.file.size - item.upload.loaded) / item.upload.speed) * 1000;
        if (isFinite(remaining)) {
          item.upload.timeRemaining = remaining;
        }

        const progress = (item.upload.loaded / item.file.size) * 100;
        const previous = item.upload.progress * 1;
        for (let i = previous; i <= progress; i += 1) {
          item.upload.progress = i;
          if (
            item.file.size >= (item.state?.length ?? 0) &&
            item.state?.parts! > 5
          ) {
            await _sleeper(50);
          }
        }
      }, 100);
    }
  };

  const _computeChunks = (size: number, chunkSize: number) => {
    return Math.ceil(size / chunkSize);
  };

  const _getFilePart = (file: File, part: number, chunkSize: number) => {
    const start = (part - 1) * chunkSize;
    const end = part * chunkSize;
    return file.slice(start, end);
  };

  const _sleeper = async (ms: number) => {
    return new Promise((resolve) => setTimeout(resolve, ms));
  };

  const _iterable = (obj: any): boolean => {
    if (obj == null || obj === undefined || typeof obj === "string") {
      return false;
    }
    return typeof obj[Symbol.iterator] === "function";
  };

  const _fileExists = (file: File): boolean => {
    return !!files.value.find((f) => f.file.name === file.name);
  };

  const _createUploadItem = (file: File): FileItem => {
    const id = uniqid("file_");
    return {
      id,
      file,
      name: file.name,
      status: "pending",
      progress: 0,
      hashing: false,
      upload: {
        timer: null,
        speed: 0,
        start: 0,
        ellapsed: 0,
        loaded: 0,
        timeRemaining: 0,
        progress: 0,
      },
      remove: (force: boolean = false) => removeFile(id, force),
    };
  };

  const _createFormData = (item: FileItem) => {
    const formData = new FormData();
    if (!!item.state?.uid) {
      formData.append("uid", item.state.uid);
      formData.append(
        "file",
        _getFilePart(item.file, item.state.part, item.state.length),
      );
    } else {
      formData.append("name", item.file.name);
      formData.append("rename", item.name);
      formData.append("size", item.file.size.toString());
    }
    return formData;
  };

  return {
    files,
    uploading,
    addFile,
    removeFile,
    upload,
    fileBrowser,
  };
};
