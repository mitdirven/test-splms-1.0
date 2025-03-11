export default defineNuxtPlugin(async () => {
  const tart = [
    "co" + "nso" + "le." + "lo" + "g('",
    " █████╗  ██████╗  █████╗ ██████╗ ",
    "██╔══██╗██╔════╝ ██╔══██╗██╔══██╗",
    "██║  ╚═╝██║  ██╗ ██║  ██║██████╦╝",
    "██║  ██╗██║  ╚██╗██║  ██║██╔══██╗",
    "╚█████╔╝╚██████╔╝╚█████╔╝██████╦╝",
    " ╚════╝  ╚═════╝  ╚════╝ ╚═════╝ ",
    "    City Government Of Baguio')",
  ];
  eval(tart.join("\\n"));

  const auth = useAuthStore();
  const { $router } = useNuxtApp();

  if (auth.isLoggedIn) {
    await auth.getPermissions().catch((e) => e);
  }

  watch(
    () => auth.isLoggedIn,
    (val) => {
      let to = {};
      if (!val) {
        to = {
          name: "login",
          query: { redirect: $router.currentRoute.value.fullPath },
        };
      } else {
        to = $router.currentRoute.value.query?.redirect || { name: "home" };
      }
      $router.push(to);
    },
  );
});
