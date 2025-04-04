<script setup lang="ts">
import { z } from "zod";
import type { Form, FormSubmitEvent } from "#ui/types";

import type { Gender, User } from "~/types/models/users";
import type { HasKey } from "~/types";

const props = defineProps<{
  api: string;
  disabled?: boolean;
}>();

const emit = defineEmits(["modifying"]);

const user = defineModel("user", {
  type: Object as PropType<User | null>,
  default: null,
});

const { $api } = useNuxtApp();
const $dayjs = useDayjs();
const toast = useToast();

const loading = ref(false);
const genderLoading = ref(false);
const genders = ref<Array<Gender>>([]);

const schema = z.object({
  first_name: z
    .string({ message: "First Name is required" })
    .min(1, { message: "First Name is required" }),
  middle_name: z.string().optional(),
  last_name: z
    .string({ message: "Last Name is required" })
    .min(1, { message: "Last Name is required" }),
  suffix: z.string().optional(),
  nickname: z.string().optional(),
  gender: z
    .string({ message: "Gender is required" })
    .min(1, { message: "Gender is required" }),
  birthdate: z.coerce.date({
    required_error: "Birthdate is required",
    invalid_type_error: "Birthdate is required",
    message: "Birthdate is required",
  }),
});

type Schema = z.output<typeof schema>;
const form = ref<Form<Schema>>();
const state = ref<
  HasKey & {
    first_name?: string;
    middle_name?: string;
    last_name?: string;
    suffix?: string;
    nickname?: string;
    gender?: string;
    birthdate?: Date;
  }
>({
  first_name: "",
  middle_name: "",
  last_name: "",
  suffix: "",
  nickname: "",
  gender: "",
  birthdate: undefined,
});

const isEdit = computed(() => !!user.value?.id);
const dobLabel = computed<string>(() =>
  !state.value.birthdate
    ? ""
    : $dayjs(state.value.birthdate ?? null).format("D MMM, YYYY"),
);

const modified = ref(false);

const getGenders = () => {
  genderLoading.value = true;
  $api
    .get("/user/genders")
    .then((response) => {
      genders.value = response.data;
    })
    .finally(() => {
      genderLoading.value = false;
    });
};

const saveProfile = (e: FormSubmitEvent<Schema>) => {
  if (modified.value) {
    return new Promise((resolve) => {
      loading.value = true;
      $api
        .patch(
          props.api,
          Object.assign({}, e.data, {
            birthdate: $dayjs(e.data.birthdate).format("YYYY-MM-DD"),
          }),
        )
        .then((response) => {
          user.value = response.data.data as User;
          // reset();

          modified.value = false;
          emit("modifying", modified.value);

          toast.add({
            title: "Profile Updated",
            description: response.data.message,
            color: "primary",
            icon: "tabler:circle-check",
          });
          resolve(response);
        })
        .catch((error) => {
          const errors = Object.keys(error.response.data.errors).map((k) => {
            return { path: k, message: error.response.data.errors[k][0] };
          });
          form.value?.setErrors(errors);
        })
        .finally(() => {
          loading.value = false;
        });
    });
  }
};

const reset = () => {
  state.value.first_name = user.value?.profile?.first_name ?? "";
  state.value.middle_name = user.value?.profile?.middle_name ?? "";
  state.value.last_name = user.value?.profile?.last_name ?? "";
  state.value.suffix = user.value?.profile?.suffix ?? "";
  state.value.nickname = user.value?.profile?.nickname ?? "";
  state.value.gender = user.value?.profile?.gender?.id ?? "";
  state.value.birthdate = user.value?.profile?.birthdate
    ? new Date(user.value?.profile?.birthdate)
    : undefined;
  form.value?.clear();
};

watch(
  state,
  (val) => {
    modified.value = Object.keys(val).some((k) => {
      if (k == "birthdate")
        return $dayjs(val[k] ?? null).isSame(user.value?.profile?.[k] ?? null);
      if (k == "gender") return val[k] != (user.value?.profile?.[k]?.id ?? "");
      return val[k] != (user.value?.profile?.[k] ?? "");
    });
    emit("modifying", modified.value);
  },
  { deep: true },
);

onMounted(() => {
  getGenders();
  reset();
});

defineExpose({
  reset: () => reset(),
  save: () => form.value?.submit(),
});
</script>

<template>
  <TForm
    label="User Information"
    :schema
    :state
    :validateOn="['submit']"
    ref="form"
    class="grid gap-4 md:grid-cols-2"
    @submit="saveProfile"
  >
    <template v-if="modified && isEdit && false">
      <template v-if="loading">
        <TIcon name="tabler:loader-2" class="h-4 w-4 animate-spin" />
      </template>
      <template v-else>
        <TButton
          label="Save"
          type="submit"
          size="xs"
          :disabled="!modified || loading"
        />
        <TButton
          label="Cancel"
          color="gray"
          variant="ghost"
          size="xs"
          :disabled="!modified || loading"
          @click="reset"
        />
      </template>
    </template>
    <TFormGroup label="First Name" name="first_name" required>
      <TInput
        v-model="state.first_name"
        placeholder="Enter First Name"
        autocomplete="off"
        :ui="{ base: 'max-w-full' }"
        :disabled="disabled || loading"
        @keyup.enter="form?.submit()"
      />
    </TFormGroup>
    <TFormGroup label="Middle Name" name="middle_name">
      <TInput
        v-model="state.middle_name"
        placeholder="Enter Middle Name"
        autocomplete="off"
        :ui="{ base: 'max-w-full' }"
        :disabled="disabled || loading"
        @keyup.enter="form?.submit()"
      />
    </TFormGroup>
    <TFormGroup label="Last Name" name="last_name" required>
      <TInput
        v-model="state.last_name"
        placeholder="Enter Last Name"
        autocomplete="off"
        :ui="{ base: 'max-w-full' }"
        :disabled="disabled || loading"
        @keyup.enter="form?.submit()"
      />
    </TFormGroup>
    <TFormGroup label="Suffix" name="suffix">
      <TInput
        v-model="state.suffix"
        placeholder="Enter Suffix"
        autocomplete="off"
        :ui="{ base: 'max-w-full' }"
        :disabled="disabled || loading"
        @keyup.enter="form?.submit()"
      />
    </TFormGroup>
    <TFormGroup v-if="false" label="Nickname" name="nickname">
      <TInput
        v-model="state.nickname"
        placeholder="Enter Nickname"
        autocomplete="off"
        :ui="{ base: 'max-w-full' }"
        :disabled="disabled || loading"
        @keyup.enter="form?.submit()"
      />
    </TFormGroup>
    <TFormGroup label="Gender" name="gender" required class="col-span-full">
      <TSelectMenu
        v-model="state.gender"
        :options="genders"
        placeholder="Select Gender"
        value-attribute="id"
        option-attribute="name"
        class="bg-gray-50 dark:bg-gray-700"
        :ui="{ wrapper: 'max-w-full' }"
        :disabled="disabled || genderLoading || loading"
        :loading="genderLoading"
      />
    </TFormGroup>
    <TFormGroup
      label="Birthddate"
      name="birthdate"
      required
      class="col-span-full"
    >
      <TPopover
        :popper="{ placement: 'bottom-start' }"
        :disabled="disabled || loading"
      >
        <TInput
          type="button"
          icon="tabler:calendar"
          v-model="dobLabel"
          class="group flex-auto"
          :disabled="disabled || loading"
          :ui="{
            base: 'text-start max-w-full',
            trailing: {
              padding: {
                sm: 'pe-2.5',
              },
            },
            icon: {
              trailing: {
                wrapper:
                  'absolute top-1/2 text-sm  text-gray-400 dark:text-gray-500 -translate-y-1/2 left-9 px-0',
                padding: {
                  sm: 'p-0',
                },
              },
            },
          }"
        >
          <template #trailing>
            {{ !state.birthdate ? "Select Date" : "" }}
          </template>
        </TInput>

        <template #panel="{ close }">
          <TDatePicker
            v-model="state.birthdate"
            is-required
            :max-date="new Date()"
            @close="close"
          />
        </template>
      </TPopover>
    </TFormGroup>
  </TForm>
</template>
