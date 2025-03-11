<script setup lang="ts">
import { TFormGroup } from "#components";
import type {
  RegionType,
  ProvinceType,
  CityType,
  BarangayType,
} from "~/types/models/address";
import type { HasKey } from "~/types";
import AddressSelect from "./tBarangay/addressSelect.vue";

const model = defineModel({
  type: Object as PropType<BarangayType | undefined>,
  default: undefined,
});

const { $api } = useNuxtApp();

const tab = ref();
const open = ref(false);
const popover = ref<InstanceType<typeof TFormGroup>>();
const w = ref(0);
const loading = ref(false);
const loaders = ref<Record<string, boolean>>({
  region: false,
  province: false,
  city: false,
  barangay: false,
});

const regions = ref<Array<RegionType>>([]);
const provinces = ref<Array<ProvinceType>>([]);
const cities = ref<Array<CityType>>([]);
const barangays = ref<Array<BarangayType>>([]);

const selected = ref<
  HasKey & {
    region?: RegionType;
    province?: ProvinceType;
    city?: CityType;
  }
>({
  region: undefined,
  province: undefined,
  city: undefined,
});

const label = computed(() => {
  return [
    selected.value.region?.name,
    selected.value.province?.name,
    selected.value.city?.name,
    model.value?.name,
  ]
    .filter(Boolean)
    .join(", ");
});

const tabs = computed(() => [
  {
    label: "Region",
    disabled: false,
  },
  {
    label: "Province",
    disabled: !selected.value.region,
  },
  {
    label: "City",
    disabled: !selected.value.province,
  },
  {
    label: "Barangay",
    disabled: !selected.value.city,
  },
]);

const observer = new ResizeObserver((entries) => {
  const first = entries[0];
  if (first) {
    w.value = first.contentRect.width;
  }
});

const loadAddress = () => {
  loading.value = true;
  Object.keys(loaders.value).forEach((k) => (loaders.value[k] = true));
  let uri = "/address/initial/city/141102000";
  if (model.value) {
    uri = `/address/initial/barangay/${model.value.code}`;
  }
  $api
    .get(uri)
    .then((response) => {
      regions.value = response.data.regions as Array<RegionType>;
      provinces.value = response.data.provinces as Array<ProvinceType>;
      cities.value = response.data.cities as Array<CityType>;
      barangays.value = response.data.barangays as Array<BarangayType>;

      const loc = response.data.city ?? response.data.barangay;

      selected.value.region = regions.value.find(
        (r) => r.code === loc.regionCode,
      );
      selected.value.province = provinces.value.find(
        (p) => p.code === loc.provinceCode,
      );
      selected.value.city = cities.value.find(
        (c) => c.code === (loc.cityCode ?? loc.code),
      );

      tab.value = 3;
    })
    .finally(() => {
      loading.value = false;
      Object.keys(loaders.value).forEach((k) => (loaders.value[k] = false));
    });
};

const selectRegion = (region: RegionType) => {
  selected.value.region = region;

  provinces.value = [];
  cities.value = [];
  barangays.value = [];

  selected.value.province = undefined;
  selected.value.city = undefined;
  model.value = undefined;
  tab.value = 1;
};

const selectProvince = (province: ProvinceType) => {
  selected.value.province = province;

  cities.value = [];
  barangays.value = [];

  selected.value.city = undefined;
  model.value = undefined;
  tab.value = 2;
};

const selectCity = (city: CityType) => {
  selected.value.city = city;

  barangays.value = [];

  model.value = undefined;
  tab.value = 3;
};

const selectBarangay = (barangay: BarangayType) => {
  model.value = barangay;
  open.value = false;
};

onMounted(() => {
  loadAddress();
  nextTick(() => {
    observer.observe(popover.value?.$el);
  });
});

onBeforeUnmount(() => {
  observer.disconnect();
});
</script>

<template>
  <TPopover
    v-model:open="open"
    :popper="{ adaptive: true }"
    class="col-span-full"
  >
    <slot
      :label="label"
      :selected="Object.assign(selected, { barangay: model })"
    >
      <TFormGroup
        ref="popover"
        label="Region, Province, City, Barangay"
        name="barangay"
        class="flex-auto"
        required
      >
        <TInput
          v-model="label"
          type="button"
          icon="tabler:map-pin"
          class="w-full"
          :loading
          :ui="{
            base: 'max-w-full text-start',
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
            {{ !selected.region ? "Select Region" : "" }}
          </template>
        </TInput>
      </TFormGroup>
    </slot>
    <template #panel="{ close }">
      <TCard
        :ui="{
          base: 'max-h-96 h-screen-95 overflow-hidden',
          divide: 'divide-y divide-gray-400/25',
          header: {
            padding: 'py-0',
          },
          body: {
            base: 'overflow-auto',
          },
        }"
        :style="{ width: `${w}px` }"
      >
        <template #header>
          <TTabs
            v-model="tab"
            :items="tabs"
            :content="false"
            class="flex-auto"
          />
        </template>
        <AddressSelect
          v-if="tab == 0"
          api="/address/regions"
          v-model:items="regions"
          v-model="selected.region"
          class="px-3 py-2"
          @select="selectRegion"
        >
          <template #option="{ option, selected }">
            <div class="flex flex-auto flex-col text-start">
              <TTypography variant="sm">
                {{ option.name }}
              </TTypography>
              <TTypography variant="xs" class="leading-none text-gray-400">
                {{ option.regionName }}
              </TTypography>
            </div>
            <TIcon v-if="selected" name="tabler:check" />
          </template>
        </AddressSelect>
        <AddressSelect
          v-if="tab == 1 && selected.region"
          :api="`/address/provinces/${selected.region?.code}`"
          :items="provinces"
          v-model="selected.province"
          class="px-3 py-2"
          @select="selectProvince"
        />

        <AddressSelect
          v-if="tab == 2 && selected.province"
          :api="`/address/cities/${selected.province?.code}`"
          :items="cities"
          v-model="selected.city"
          class="px-3 py-2"
          @select="selectCity"
        />
        <AddressSelect
          v-if="tab == 3 && selected.city"
          :api="`/address/barangays/${selected.city?.code}`"
          :items="barangays"
          v-model="selected.barangay"
          class="px-3 py-2"
          @select="selectBarangay"
        >
          <template #option="{ option, selected }">
            <div class="flex flex-auto flex-col text-start">
              <TTypography variant="sm">
                {{ option.name }}
              </TTypography>
              <TTypography variant="xs" class="leading-none text-gray-400">
                {{ option.oldName }}
              </TTypography>
            </div>
            <TIcon v-if="selected" name="tabler:check" />
          </template>
        </AddressSelect>
      </TCard>
    </template>
    <template #footer> </template>
  </TPopover>
</template>

<style scoped></style>
