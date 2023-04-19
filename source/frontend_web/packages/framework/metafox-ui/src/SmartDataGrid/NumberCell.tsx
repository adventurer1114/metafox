/**
 * @type: ui
 * name: dataGrid.cell.NumberCell
 */

import { useGlobal } from '@metafox/framework';
import { get } from 'lodash';

// todo moved this column to base size.
export default function NumberCell({ id, row, colDef: { field } }) {
  const { i18n } = useGlobal();

  return i18n.formatNumber(get(row, field, 0));
}
