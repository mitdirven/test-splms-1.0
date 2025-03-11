const isValidTag = (tag: string) => {
  return (
    document.createElement(tag).toString() !== "[object HTMLUnknownElement]"
  );
};

const uniqid = (prefix: string = "", random: boolean = false): string => {
  const sec = Date.now() * 1000 + Math.random() * 1000;
  const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");
  return `${prefix}${id}${
    random ? `.${Math.trunc(Math.random() * 100000000)}` : ""
  }`;
};

const msToReadable = (milliseconds: number) => {
  const seconds = Math.floor(milliseconds / 1000);

  const _s = (num: number) => {
    return num > 1 ? "s" : "";
  };

  return {
    year: Math.floor(seconds / 31536000),
    day: Math.floor((seconds % 31536000) / 86400),
    hour: Math.floor(((seconds % 31536000) % 86400) / 3600),
    minute: Math.floor((((seconds % 31536000) % 86400) % 3600) / 60),
    second: (((seconds % 31536000) % 86400) % 3600) % 60,
    get str() {
      return Object.keys(this)
        .filter(
          (key) =>
            ["year", "day", "hour", "minute", "second"].indexOf(key) > -1 &&
            (this[key] as number) > 0,
        )
        .map((key) => {
          return `${this[key]} ${key}${_s(this[key] as number)}`;
        })
        .join(" ")
        .trim();
    },
  } as Record<string, number | string>;
};

const formatSize = (size: number) => {
  const units = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
  let i = 0;
  while (size >= 1024 && i < units.length - 1) {
    size /= 1024;
    i++;
  }
  return `${size.toFixed(2)} ${units[i]}`;
};

const CapitalizeFirstLetter = (str: string): string => {
  return str
    ? str
        .toLowerCase()
        .replace(/(^\w{1})|(\s{1}\w{1})/g, (match) => match.toUpperCase())
    : "";
};

// Mainly used for permission names
const dashToHuman = (str: string) => {
  return !!str
    ? CapitalizeFirstLetter(str.replaceAll("-", " ").replaceAll("_", " - "))
    : "";
};

const extractHtmlContent = (s: string, space: boolean = false): string => {
  var span = document.createElement("span");
  span.innerHTML = s;
  if (space) {
    var children = span.querySelectorAll("*");
    for (var i = 0; i < children.length; i++) {
      if (children[i].textContent) children[i].textContent += " ";
      else (children[i] as HTMLElement).innerText += " ";
    }
  }
  return [span.textContent || span.innerText].toString().replace(/ +/g, " ");
};

export const useUtils = () => ({
  isValidTag,
  uniqid,
  msToReadable,
  formatSize,
  CapitalizeFirstLetter,
  dashToHuman,
  extractHtmlContent,
});
