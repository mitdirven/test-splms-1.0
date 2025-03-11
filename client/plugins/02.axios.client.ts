import axios from "axios";

export default defineNuxtPlugin(async (nuxtApp) => {
  const config = useRuntimeConfig();

  const api = axios.create({
    withCredentials: true,
    withXSRFToken: true,
    headers: {
      Accept: "application/json",
      Authorization: null,
    },
    baseURL: config.public.baseUrl,
  });

  api.interceptors.request.use(
    (response) => Promise.resolve(response),
    (error) => Promise.reject(error),
  );

  api.interceptors.response.use(
    (response) => Promise.resolve(response),
    (error) => {
      if (error.response.status === 401) {
        const $auth = useAuthStore();
        const { $router } = useNuxtApp();
        if ($auth.isLoggedIn) {
          $auth.reset();
        }

        if ($router.currentRoute.value.name !== "login") {
          $router.push({
            name: "login",
            query: { redirect: $router.currentRoute.value.fullPath },
          });
        }
      }

      if (!error.response?.data?.error_code) {
        const toast = useToast();
        toast.add({
          title: error.response?.data?.message ?? error.message,
          description: error.response ? error.message : null,
          color: "red",
          icon: "tabler:exclamation-circle",
        });
      }
      return Promise.reject(error);
    },
  );

  await api.get("/csrf-cookie").catch((e) => e);

  return { provide: { api } };
});
