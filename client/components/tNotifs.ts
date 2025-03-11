import { h, type PropType } from "vue";
import { twMerge, type ClassNameValue } from "tailwind-merge";
import { Icon } from "#components";

export default defineComponent({
  name: "t-notifs",
  props: {
    icon: {
      type: String,
      default: "tabler:alert-octagon",
    },
    color: {
      type: String as PropType<ClassNameValue>,
      default: "text-orange-500 dark:text-orange-400",
    },
    date: {
      type: String,
      default: new Date().toISOString(), // Default to current date
    },
    title: {
      type: String,
      required: true,
    },
    message: {
      type: String,
      required: true,
    },
  },
  setup(props, { slots }) {
    const formatDate = (date: string) => {
      return new Date(props.date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    };
    return () => {
      const icon = h(Icon, {
        name: props.icon,
        class: twMerge("h-8 w-8", props.color),
      });
      const date = h("TTypography", { variant: "span" }, [
        formatDate(props.date),
      ]);
      const header = h("div", { class: "flex items-center gap-3" }, [
        icon,
        date,
      ]);

      const title = h(
        "TTypography",
        {
          variant: "h6",
          class: twMerge("text-base font-semibold", props.color),
        },
        [props.title],
      );
      const message = h("TTypography", { variant: "md" }, [
        slots.default?.() ?? props.message,
      ]);

      const body = h(
        "div",
        {
          class:
            "ml-4 space-y-1 border-l-2 border-gray-200 pb-4 pl-7 group-last:border-transparent dark:border-gray-600 dark:group-last:border-transparent",
        },
        [title, message],
      );

      const wrapper = h(
        "div",
        { class: "group flex flex-col items-start gap-1 last:pb-4" },
        [header, body],
      );
      return wrapper;
    };
  },
});
