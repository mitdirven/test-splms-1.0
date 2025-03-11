import type { HasKey } from "~/types";

export type UploadOptions = {
  api: string;
  checkIntegrity?: boolean;
  multiple?: boolean;

  sleep?: {
    long: number;
    short: number;
    interval: number;
  };
};

export type FileItem = HasKey & {
  id: string;
  file: File;
  name: string;
  status: "pending" | "uploading" | "complete" | "error" | "cancelled";
  state?: HasKey & {
    uid: string;
    part: number;
    parts: number;
    length: number;
    progress: number;
  };
  upload: {
    timer: NodeJS.Timeout | null;
    speed: number;
    start: number;
    ellapsed: number;
    loaded: number;
    timeRemaining: number;
    progress: number;
  };
  remove: (force: boolean) => void;
  matchedHash?: boolean;
  hashing: boolean;
};
