/**
 * @type: ui
 * name: CategoryTitle
 */
import { useGetItem } from '@metafox/framework';

export default function CategoryTitle({ identity, alt = null }) {
  const category = useGetItem(identity);

  if (!category) return alt;

  return category.title ?? category.name ?? alt;
}
