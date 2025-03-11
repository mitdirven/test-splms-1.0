<script setup lang="ts">
import { boolean } from "zod";
import DatePicker from "~/components/DatePicker.vue";
const { customize } = useIcon();

type Duration = {
  days?: number;
  months?: number;
  years?: number;
};

const props = defineProps<{
  q?: string;
  dropwdown?: Array<any>;
  enableSearch?: boolean;
  enableDropdown?: boolean;
}>();

const reset = ref(false);

const emit = defineEmits(["update:q"]);

const $dayjs = useDayjs();

const ranges = [
  { label: "Last 7 days", duration: { days: 7 } },
  { label: "Last 14 days", duration: { days: 14 } },
  { label: "Last 30 days", duration: { days: 30 } },
  { label: "Last 3 months", duration: { months: 3 } },
  { label: "Last 6 months", duration: { months: 6 } },
  { label: "Last year", duration: { years: 1 } },
];

const defaultRange = {
  start: $dayjs().subtract(7, "day").format("YYYY-MM-DD"),
  end: $dayjs().format("YYYY-MM-DD"),
};
const selected = ref({ ...defaultRange });

const isRangeSelected = computed(() => {
  return (
    selected.value.start !== defaultRange.start ||
    selected.value.end !== defaultRange.end
  );
});

function dateRangeSelected(duration: Duration) {
  const start = $dayjs()
    .subtract(duration.days || 0, "day")
    .subtract(duration.months || 0, "month")
    .subtract(duration.years || 0, "year")
    .format("YYYY-MM-DD");

  return (
    selected.value.start === start &&
    selected.value.end === $dayjs().format("YYYY-MM-DD")
  );
}

function selectRange(duration: Duration) {
  selected.value = {
    start: $dayjs()
      .subtract(duration.days || 0, "day")
      .subtract(duration.months || 0, "month")
      .subtract(duration.years || 0, "year")
      .format("YYYY-MM-DD"),
    end: $dayjs().format("YYYY-MM-DD"),
  };
}

function updateQ(event: Event) {
  const target = event.target as HTMLInputElement;
  emit("update:q", target.value);
}

function resetSelection() {
  selected.value = { ...defaultRange };
  reset.value = false;
}
</script>
<template>
  <div class="col-span-full row-span-1 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <TInput
        v-if="enableSearch"
        :model-value="q"
        @input="updateQ($event)"
        icon="tabler:search"
        size="sm"
        color="white"
        trailing
        placeholder="Search..."
      />

      <TPopover v-if="!enableSearch" :popper="{ placement: 'bottom-start' }">
        <TButton icon="tabler:calendar">
          {{ $dayjs(selected.start).format("D MMM, YYYY") }} -
          {{ $dayjs(selected.end).format("D MMM, YYYY") }}
        </TButton>

        <template #panel="{ close }">
          <div
            class="flex items-center divide-gray-200 dark:divide-gray-800 sm:divide-x"
          >
            <div class="hidden flex-col py-4 sm:flex">
              <TButton
                v-for="(range, index) in ranges"
                :key="index"
                :label="range.label"
                color="gray"
                variant="ghost"
                class="rounded-none px-6"
                :class="[
                  dateRangeSelected(range.duration)
                    ? 'bg-gray-100 dark:bg-gray-800'
                    : 'hover:bg-gray-50 dark:hover:bg-gray-800/50',
                ]"
                truncate
                @click="selectRange(range.duration)"
              />
            </div>

            <DatePicker v-model="selected" @close="close" />
          </div>
        </template>
      </TPopover>

      <!-- Button will appear if selected a date range, this will reset the selection -->
      <button v-if="isRangeSelected" @click="resetSelection">
        <TIcon
          name="tabler:refresh"
          class="text-primary h-6 w-6"
          :customize="(c: string) => customize(c, { strokeWidth: 2 })"
        />
      </button>
    </div>
    <div class="flex items-center gap-3">
      <TDropdown
        :items="dropwdown"
        :popper="{ placement: 'bottom-end' }"
        v-if="enableDropdown"
      >
        <TButton
          color="white"
          label="Select Location"
          trailing-icon="tabler:chevron-right"
          size="md"
          :ui="{
            base: 'justify-between !shadow-none',
            padding: { md: 'py-1.5' },
          }"
        />
      </TDropdown>
      <TButton
        color="white"
        label="CSV"
        trailing-icon="tabler:file-cv"
        size="md"
        :ui="{
          base: '!shadow-none',
          padding: { md: 'py-1.5' },
          icon: { base: 'text-gray-500 dark:text-gray-300' },
        }"
      />
      <TButton
        color="white"
        label="PDF"
        trailing-icon="tabler:file-type-pdf"
        size="md"
        :ui="{
          base: '!shadow-none',
          padding: { md: 'py-1.5' },
          icon: { base: 'text-gray-500 dark:text-gray-300' },
        }"
      />
      <TButton
        color="white"
        label="Excel"
        trailing-icon="tabler:file-spreadsheet"
        size="md"
        :ui="{
          base: '!shadow-none',
          padding: { md: 'py-1.5' },
          icon: { base: 'text-gray-500 dark:text-gray-300' },
        }"
      />
      <TButton
        color="white"
        label="Print"
        trailing-icon="tabler:printer"
        size="md"
        :ui="{
          base: '!shadow-none',
          padding: { md: 'py-1.5' },
          icon: { base: 'text-gray-500 dark:text-gray-300' },
        }"
      />
    </div>
  </div>
</template>
