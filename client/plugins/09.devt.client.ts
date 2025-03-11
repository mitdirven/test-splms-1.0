import { addListener, launch } from "devtools-detector";
import type { Router } from "vue-router";

export default defineNuxtPlugin((nuxtApp) => {
  const view = document.createElement("div");
  const $auth = useAuthStore();
  const $router: Router = nuxtApp.$router as Router;
  document.body.appendChild(view);
  // 1. add listener
  //   addListener((isOpen) => {
  //     if ($auth.isLoggedIn) {
  //       $auth.logout();
  //     }
  //     $router.push({ name: "login" });
  //     console.log("devtools-detector", isOpen);
  //   });
  //   // 2. launch detect
  //   launch();
});
