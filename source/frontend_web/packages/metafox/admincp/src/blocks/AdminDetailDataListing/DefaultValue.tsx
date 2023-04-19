/**
 * @type: ui
 * name: dataListing.value.default
 */

export default function BasicCell({ format, value }) {
  if (!value) {
    return null;
  }

  return value;
}
