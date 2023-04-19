/**
 * @type: dialog
 * name: photo.dialog.photoView
 */
import { connect, GlobalState } from '@metafox/framework';
import { findIndex, get } from 'lodash';
import Base from './Base';

const mapStateToProps = (
  state: GlobalState,
  { photo_set, identity, photo_id }: any
) => {
  const item = get(state, identity);

  if (!item) {
    return {};
  }

  const photos = get(state, `photo.entities.photo_set.${photo_set}.photos`);
  const user = item?.user ? get(state, item.user) : undefined;
  const count = photos?.length;
  const pos = 1 < count ? findIndex(photos, (x: string) => x === identity) : -1;
  const user_tags = item.user_tags
    ? item.user_tags.map((x: string) => get(state, x)).filter(Boolean)
    : [];

  const result = {
    item,
    photos,
    user,
    photo_id,
    user_tags,
    nextUrl: undefined,
    prevUrl: undefined
  };

  if (-1 < pos) {
    const prefixUrl = photo_set ? `/photo/${photo_set}` : '/photo';

    if (pos < count - 1) {
      const next = photos[pos + 1];

      result.nextUrl = `${prefixUrl}/${next.replace(
        'photo.entities.photo.',
        ''
      )}`;
    }

    if (0 < pos) {
      const prev = photos[pos - 1];

      result.prevUrl = `${prefixUrl}/${prev.replace(
        'photo.entities.photo.',
        ''
      )}`;
    }
  }

  return result;
};

export default connect(mapStateToProps)(Base);
