import type { HasKey } from "../utils";
import type { PermissionItem } from "./permission";

export type RoleItem = HasKey &
  PermissionItem & {
    level: number;
    color: string;
    permissions: Array<PermissionItem>;
  };
