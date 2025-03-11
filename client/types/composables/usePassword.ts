import type { HasKey } from "../utils";

export type PasswordGenerateOptions = HasKey & {
  length: number;
  letters: boolean;
  mixedCase: boolean;
  numbers: boolean;
  symbols: boolean;
  excludeSimilarCharacters: boolean;
  exclude: string;
  excludeSimilarCharactersThreshold: number;
  excludeSimilarCharactersExclude: string;
};
