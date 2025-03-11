import type { StringList } from "./utils";

export type Address = {
  main: boolean;
  location: string;
  barangay: string;
  zipcode: string;
  type: string;
};

export type Profile = {
  first_name: string | null;
  last_name: string | null;
  middle_name: string | null;
  suffix: string | null;
  nickname: string | null;
  full_name: string | null;
  birthdate?: string;
  gender?: string;
  addresses?: Array<Address>;
};

export type User = {
  email: string | null;
  username: string | null;
  active: boolean;
  verified: string | null;
  roles: StringList;
  permissions: StringList;
  profile?: Profile | null;
};
