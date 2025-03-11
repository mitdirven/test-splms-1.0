import type { AxiosInstance, AxiosResponse } from "axios";
import defu from "defu";

export type SearcherOptions = {
  api: string;
  limit?: number;
  method?: string | "get" | "post";
  appendToUrl?: boolean;
  onPageChange?: (page: number) => void;
  onSearch?: (response: AxiosResponse) => void;
  onFail?: (error: any) => void;
};

export type SearchParams = { [key: string]: any };

export const useSearcher = <T extends SearchParams>(
  options: SearcherOptions | ComputedRef<SearcherOptions>,
) => {
  const $route = useRoute();
  const $router = useRouter();
  const { $api } = useNuxtApp();

  const _default = {
    limit: 25,
    method: "get",
    appendToUrl: false,
    onPageChange: (page: number) => {},
    onSearch: (response: AxiosResponse) => {},
    onFail: (error: any) => {},
  };
  const _options = computed<SearcherOptions>(() =>
    defu(toValue(options), _default),
  );

  const _filterParams = (
    p: SearchParams,
    excluded: string[] = ["page", "limit"],
  ): T => {
    let result = {};
    Object.keys(p)
      .filter((key) => excluded.indexOf(key) < 0)
      .forEach((key) => {
        Object.assign(result, { [key]: p[key] });
      });
    return result as T;
  };

  const params = ref<T>(Object.assign({}, _filterParams($route.query)) as T);
  const pagination = ref(
    Object.assign(
      {
        total: 0,
        page: 1,
        limit: _options.value.limit,
        reset() {
          this.page = 1;
          this.limit = _options.value.limit;
        },
      },
      {
        page: Math.max(
          1,
          Number(_options.value.appendToUrl ? $route.query.page : 1) || 1,
        ),
        limit: Math.max(
          1,
          Number(
            _options.value.appendToUrl
              ? $route.query.limit
              : _options.value.limit,
          ) || _options.value.limit!,
        ),
      },
    ),
  );

  const loading = ref(false);
  const paginationChangedFromRoute = ref(false);
  const routeChangedFromSearch = ref(false);
  const bounce = ref(true);

  const search = async (): Promise<AxiosResponse> => {
    return new Promise((resolve, reject) => {
      loading.value = true;
      const routeParams = Object.assign(
        {
          page: pagination.value.page,
          limit: pagination.value.limit,
        },
        params.value,
      ) as SearchParams;

      _appendRouteParams(routeParams);

      ($api as AxiosInstance & { [key: string]: any })
        [_options.value.method!](
          _options.value.api,
          (_options.value.method == "get"
            ? { params: routeParams }
            : routeParams) as Object,
        )
        .then((response: AxiosResponse) => {
          pagination.value.total = response.data.total || 1;
          routeChangedFromSearch.value = false;
          paginationChangedFromRoute.value = false;
          if (response.data.page != pagination.value.page) {
            bounce.value = false;
            pagination.value.page = Math.max(1, response.data.page);
            $router.replace({
              name: $route.name,
              query: Object.assign(routeParams, {
                page: pagination.value.page,
              }),
            });
          }

          _options.value.onSearch?.(response);
          resolve(response);
        })
        .catch((error: any) => {
          _options.value.onFail?.(error);
          reject(error);
        })
        .finally(() => {
          loading.value = false;
        });
    });
  };

  const _appendRouteParams = (routeParams: SearchParams, method?: string) => {
    if (_options.value.appendToUrl) {
      const _method = !$route.query.page ? "replace" : "push";
      routeChangedFromSearch.value = true;
      $router[_method]({ name: $route.name, query: routeParams });
    }
  };

  const debounced = useDebounceFn(() => {
    if (bounce.value) {
      _options.value.onPageChange?.(pagination.value.page) ?? search();
    }
    bounce.value = true;
  }, 50);

  watch([() => pagination.value.page, () => pagination.value.limit], () => {
    if (!paginationChangedFromRoute.value) {
      debounced();
    }
  });
  watch(
    () => $route.query,
    (val, old) => {
      const same = JSON.stringify(val) == JSON.stringify(old);
      if (
        !same &&
        !!old.page &&
        _options.value.appendToUrl &&
        !routeChangedFromSearch.value
      ) {
        bounce.value = true;
        paginationChangedFromRoute.value = true;
        params.value = _filterParams(val);
        pagination.value.page = Math.max(1, Number(val.page || 1));
        pagination.value.limit = Math.max(
          1,
          Number(val.limit || _options.value.limit),
        );
        debounced();
      }
    },
    { deep: true },
  );

  return {
    pagination,
    params,
    loading,
    search,
    searcher: async (
      _params?: SearchParams | ComputedRef<SearchParams>,
    ): Promise<AxiosResponse> => {
      warn(
        "The 'searcher' method is deprecated. Please use 'search' method instead.",
      );
      params.value = toValue(_params);
      return await search();
    },
  };
};
