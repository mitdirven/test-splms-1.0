import type { PluginAPI } from "tailwindcss/types/config";

export function twUtilities({ addUtilities }: PluginAPI) {
  const newUtilities = {
    ".no-scrollbar::-webkit-scrollbar": {
      display: "none",
    },
    ".no-scrollbar": {
      msOverflowStyle: "none",
      scrollbarWidth: "none",
    },
    ".scrollbar-thin": {
      "scrollbar-width": "thin",
    },
  };

  addUtilities(newUtilities);
}
