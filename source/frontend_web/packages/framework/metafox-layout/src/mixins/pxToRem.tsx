// Tell Material-UI what's the font-size on the html element.
// 16px is the default font-size used by browsers.
const htmlFontSize = 16;

export default function pxToRem() {
  return (size: number): string => {
    return `${size / htmlFontSize}rem`;
  };
}
