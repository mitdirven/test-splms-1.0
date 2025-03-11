import { h } from "vue";
import type { BreadcrumbLink } from "#ui/types";
import { Icon, TBreadcrumb } from "#components";

export default defineComponent({
  name: "t-page-title",
  props: {
    icon: {
      type: String,
      default: null,
    },
    title: {
      type: String,
      default: null,
    },
    breadcrumbs: {
      type: Array<BreadcrumbLink>,
      default: null,
    },
  },
  setup(props, { slots }) {
    return () => {
      const icon = !!props.icon
        ? h(Icon, { name: props.icon, class: "text-2xl" })
        : null;
      const title = h(
        "div",
        { class: "flex flex-auto items-center gap-2" },
        slots.default?.() ?? [icon, props.title],
      );
      const crumbs = h(
        "div",
        { class: "flex items-center gap-2" },
        h(TBreadcrumb, { links: props.breadcrumbs }),
      );
      const wrapper = h(
        "div",
        { class: "flex items-center gap-2 px-6 py-2 dark:bg-gray-800" },
        [title, crumbs],
      );
      return wrapper;
    };
  },
});
