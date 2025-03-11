import type { HasKey } from "../utils";
import type { BaseModel, Common } from "./base";
import type { PermissionItem } from "./permission";
import type { RoleItem } from "./roles";

export type Gender = HasKey &
  Common & {
    description?: string | null;
    active?: boolean;
  };

export type AddressType = Common;

export type UserRole = Pick<RoleItem, "id" | "name" | "color">;
export type UserPermission = Pick<PermissionItem, "id" | "name">;

export type LocationItem = HasKey & {
  code: string;
  name: string;
};

export type Address = HasKey &
  Required<BaseModel> & {
    type?: AddressType | null;
    readonly full: string;
    location: string;
    zipCode: string;
    isMain: boolean;
    barangay: LocationItem;
    city: LocationItem;
    province: LocationItem;
    region: LocationItem;
    islandGroup: LocationItem;
  };

export type Profile = HasKey & {
  first_name: string | null;
  last_name: string | null;
  middle_name: string | null;
  suffix: string | null;
  nickname: string | null;
  readonly full_name: string | null;
  birthdate?: string;
  gender?: Gender | null;
  addresses?: Array<Address> | null;
  images?: Array<ProfileImage>;
};

export type User = HasKey &
  Required<BaseModel> & {
    active: boolean;
    username: string;
    email?: string | null;
    verified?: string | null;
    roles: Array<UserRole | string>;
    permissions: Array<UserPermission | string>;
    profile?: Profile | null;
  };

export type ProfileImage = HasKey & {
  alt: string;
  ext: string;
  mime: string;
  primary: boolean;
  size: number;
  url: Record<string, string>;
};
