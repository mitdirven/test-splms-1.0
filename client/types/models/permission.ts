import type { HasKey } from "../utils";

export type PermissionItem = HasKey & {
  id: string;
  name: string;
  description: string;
  date: string;
};
