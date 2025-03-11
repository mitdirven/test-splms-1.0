export const loadingIcon: string = "i-tabler-loader-2";

export default {
  button: {
    default: {
      loadingIcon,
    },
  },
  input: {
    default: {
      loadingIcon,
    },
  },
  select: {
    default: {
      loadingIcon,
      trailingIcon: "i-tabler-chevron-down",
    },
  },
  selectMenu: {
    default: {
      selectedIcon: "i-tabler-check",
    },
  },
  notification: {
    default: {
      closeButton: {
        icon: "i-tabler-x",
      },
    },
  },
  commandPalette: {
    default: {
      icon: "i-tabler-search",
      loadingIcon,
      selectedIcon: "i-tabler-check",
      emptyState: {
        icon: "i-tabler-search",
      },
    },
  },
  table: {
    default: {
      sortAscIcon: "i-tabler-sort-ascending",
      sortDescIcon: "i-tabler-sort-descending",
      sortButton: {
        icon: "i-tabler-switch-horizontal",
      },
      loadingState: {
        icon: loadingIcon,
      },
      emptyState: {
        icon: "i-tabler-database",
      },
    },
  },
  pagination: {
    default: {
      firstButton: {
        icon: "i-tabler-chevron-left",
      },
      prevButton: {
        icon: "i-tabler-arrow-left",
      },
      nextButton: {
        icon: "i-tabler-arrow-right",
      },
      lastButton: {
        icon: "i-tabler-chevron-right",
      },
    },
  },
  accordion: {
    default: {
      openIcon: "i-tabler-chevron-down",
    },
  },
  breadcrumb: {
    default: {
      divider: "i-tabler-chevron-right",
    },
  },
};
