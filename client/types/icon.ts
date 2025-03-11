import type { HasKey } from "./utils";

export type IIconProps = {
  name: string;
  mode?: "svg" | "css" | null;
  size?: number | string | null;
  customize?: Function | null;
};

export type IUseIcon = HasKey & {
  strokeWidth?: number;
  strokeColor?: string;
  fill?: string;
  animationDuration?: number;
  opacity?: number;
};
