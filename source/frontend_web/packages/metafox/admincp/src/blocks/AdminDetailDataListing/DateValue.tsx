/**
 * @type: ui
 * name: dataListing.value.time
 */

import { useGlobal } from '@metafox/framework';

export default function BasicCell({ format, value }) {
  const { moment } = useGlobal();

  if (!value) {
    return null;
  }

  const date = moment(value);

  return date.format(format);
}
