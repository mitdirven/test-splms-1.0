export default defineComponent({
  name: "t-collapse",
  props: {
    tag: {
      type: String,
      default: "div",
    },
    modelValue: {
      type: Boolean,
      default: true,
    },
  },
  emits: ["update:modelValue"],
  setup(props, { emit, slots }) {
    const containerRef = ref<HTMLElement>();
    const transitioning = ref<boolean>(false);

    const open = computed({
      get: () => props.modelValue,
      set: (val) => emit("update:modelValue", val),
    });
    const show = computed(() => transitioning.value || open.value);

    watch(open, () => {
      transitioning.value = true;
    });

    const transitionend = () => {
      transitioning.value = false;
    };

    onMounted(() =>
      nextTick(() => {
        containerRef.value?.addEventListener("transitionend", transitionend);
      }),
    );

    onBeforeUnmount(() => {
      containerRef.value?.removeEventListener("transitionend", transitionend);
    });

    const s = computed(() => {
      return show.value ? slots.default?.() : undefined;
    });

    return () =>
      h(
        "div",
        {
          class: "grid select-none transition-all duration-300",
          style: {
            gridTemplateRows: open.value ? "1fr" : "0fr",
          },
          ref: containerRef,
        },
        h(
          "div",
          {
            class: [
              "row-[1_/_span_2] select-text",
              !open.value || transitioning.value
                ? "overflow-hidden"
                : "overflow-auto",
            ],
          },
          s.value,
        ),
      );
  },
});
