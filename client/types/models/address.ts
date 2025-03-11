import type { HasKey } from "../utils";
import type { Common as Model } from "./base";

interface Common extends HasKey {
  name: string;
  code: string;
  psgc10DigitCode?: string;
}

export type IslandGroupType = Omit<Common, "psgc10DigitCode">;

export type RegionType = Common & {
  regionName: string;
  islandGroupCode?: string;
};

export type ProvinceType = Common & {
  isDistrict: boolean;
  regionCode: string;
  islandGroupCode: string;
};

export type CityType = Common & {
  oldName: string;
  isMunicipality: Boolean;
  isCapital: Boolean;
  provinceCode: string;
  regionCode: string;
  islandGroupCode: string;
};

export type BarangayType = Common & {
  oldName?: string;
  cityCode: string;
  provinceCode: string;
  regionCode: string;
  islandGroupCode: string;

  district?: number;
  telephone_number?: string;
  contact_number?: string;
  lng?: number;
  lat?: number;
  dru?: number;
};
