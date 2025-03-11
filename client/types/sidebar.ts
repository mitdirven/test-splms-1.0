import type { RouteLocationRaw } from "vue-router";

export type AvatarOptions = {
  label?: string;
  icon?: string;
  divider?: boolean;
  to?: RouteLocationRaw;
  hidden?: boolean | ComputedRef<boolean>;
  action?: () => void;
};

export type MenuOption = AvatarOptions & {
  active?: boolean;
  exact?: boolean;
  children?: Array<MenuOption>;
};
