import type { Config } from "tailwindcss";
import {
  colors,
  animation,
  keyframes,
  height as twHeight,
  width as twWidth,
  backgroundImage,
  backgroundPosition,
  backgroundSize,
} from "./config/tw";
import twScrollBar from "tailwind-scrollbar";

export default {
  content: [
    "./spa-loading-template.html",
    "./directives/**/*.{vue,js,ts,jsx,tsx}",
  ],
  darkMode: "class",

  theme: {
    extend: {
      fontFamily: {
        publicSans: ["PublicSans", "Arial", "sans-serif"],
      },

      screens: {
        "3xl": "1920px",
        "4xl": "2560px",
        "5xl": "3840px",
      },

      aspectRatio: {
        auto: "auto",
        square: "1 / 1",
        video: "16 / 9",
      },

      // @ts-ignore
      colors,
      // @ts-ignore
      animation,
      // @ts-ignore
      keyframes,
      // @ts-ignore
      height: twHeight,
      // @ts-ignore
      maxHeight: twHeight,
      // @ts-ignore
      minHeight: twHeight,
      // @ts-ignore
      width: twWidth,
      // @ts-ignore
      minWidth: twWidth,
      // @ts-ignore
      maxWidth: twWidth,
      // @ts-ignore
      backgroundImage,
      // @ts-ignore
      backgroundSize,
      // @ts-ignore
      backgroundPosition,
    },
  },
  safelist: ["iconify"],
  plugins: [
    twScrollBar({ nocompatible: true, preferredStrategy: "pseudoelements" }),
  ],
} satisfies Config;
