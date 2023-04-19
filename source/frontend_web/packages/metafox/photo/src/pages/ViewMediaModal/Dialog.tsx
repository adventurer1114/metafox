/**
 * @type: dialog
 * name: media.dialog.mediaView
 */
import { connect, GlobalState } from '@metafox/framework';
import { findIndex, get } from 'lodash';
import Base from './Base';

const mapStateToProps = (
  state: GlobalState,
  { photo_set, photo_album, identity, photo_id, media_type }: any
) => {
  const item = get(state, identity);

  if (!item) {
    return {};
  }

  const photos = photo_album
    ? get(state, `photo.entities.photo_album.${photo_album}.photos`)
    : get(state, `photo.entities.photo_set.${photo_set}.photos`);
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
    mediaType: media_type,
    nextUrl: undefined,
    prevUrl: undefined
  };

  if (-1 < pos) {
    let prefixUrl = '/photo';

    if (photo_set) {
      prefixUrl = `/media/${photo_set}`;
    }

    if (photo_album) {
      prefixUrl = `/media/album/${photo_album}`;
    }

    if (pos < count - 1) {
      const next = photos[pos + 1];

      const resource_name = next.split('.')[2];

      result.nextUrl = `${prefixUrl}/${resource_name}/${next.replace(
        `${resource_name}.entities.${resource_name}.`,
        ''
      )}`;
    }

    if (0 < pos) {
      const prev = photos[pos - 1];

      const resource_name = prev.split('.')[2];

      result.prevUrl = `${prefixUrl}/${resource_name}/${prev.replace(
        `${resource_name}.entities.${resource_name}.`,
        ''
      )}`;
    }
  }

  return result;
};

export default connect(mapStateToProps)(Base);
