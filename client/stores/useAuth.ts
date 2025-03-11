import type { StringList } from "~/types";
import type {
  Profile,
  User,
  ProfileImage,
  UserRole,
} from "~/types/models/users";

export const useAuthStore = defineStore("auth", () => {
  const email = ref<string | null>();
  const username = ref<string | null>();
  const active = ref<boolean>(false);
  const verified = ref<string | null>();
  const roles = ref<Array<Omit<UserRole, "id">>>([]);
  const permissions = ref<StringList>([]);
  const images = ref<Array<ProfileImage>>([]);

  const profile = ref<Profile | null>();

  const isLoggedIn = computed(() => username.value !== null);
  const hasProfileName = computed(() => !!profile.value?.full_name);

  const reset = () => {
    email.value = null;
    username.value = null;
    active.value = false;
    verified.value = null;
    roles.value = [];
    permissions.value = [];
    profile.value = null;
  };

  const setUser = (data: User) => {
    email.value = data.email;
    username.value = data.username;
    active.value = data.active;
    verified.value = data.verified;
    roles.value = (data.roles as Array<Omit<UserRole, "id">>) ?? [];
    permissions.value = (data.permissions as StringList) ?? [];
    profile.value = {
      ...data.profile,
    } as Profile;
    images.value = (data.profile?.images as Array<ProfileImage>) ?? [];
  };

  const login = async (payload: Object) => {
    const { $api } = useNuxtApp();
    return new Promise((resolve, reject) => {
      $api
        .post("/auth/login", payload)
        .then((res) => {
          setUser(res.data.data);
          resolve(res);
        })
        .catch((error) => {
          reject(error);
        });
    });
  };

  /**
   * Sign out user
   * @returns {Promise}
   */
  const logout = async (): Promise<any> => {
    const { $api } = useNuxtApp();
    return new Promise((resolve, reject) => {
      $api
        .post("/auth/logout")
        .then((res) => {
          resolve(res);
        })
        .catch((error) => {
          reject(error);
        })
        .finally(reset);
    });
  };

  const getPermissions = async () => {
    const { $api } = useNuxtApp();
    return new Promise((resolve, reject) => {
      $api
        .get("/auth/permissions")
        .then((res) => {
          setUser(res.data.data);
          resolve(res);
        })
        .catch((error) => {
          reject(error);
        });
    });
  };

  return {
    email,
    username,
    verified,
    active,
    isLoggedIn,
    hasProfileName,
    roles,
    permissions,
    profile,
    login,
    logout,
    getPermissions,
    setUser,
    reset,
  };
});
