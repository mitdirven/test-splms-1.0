import type { HasKey } from "../utils";

export type BaseModel = HasKey & {
  id?: string;
};

export type Common = HasKey &
  Required<BaseModel> & {
    name: string;
  };
