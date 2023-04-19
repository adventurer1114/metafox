import {
  BasicFileItem,
  useGlobal,
  useResourceConfig
} from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { Button } from '@mui/material';
import { concat, uniq, uniqueId } from 'lodash';
import React from 'react';
type Props = {
  setItems: any;
  variant: 'default' | 'listing';
};
export default function AddMorePhotoButtons({ setItems, variant }: Props) {
  const { i18n } = useGlobal();
  const inputRef = React.useRef<HTMLInputElement>();
  const onClick = React.useCallback(() => {
    inputRef.current.click();
  }, [inputRef]);

  const handleChange = React.useCallback(() => {
    const files = inputRef.current.files;

    if (!files.length) return;

    const fileItems: BasicFileItem[] = [];

    for (let i = 0; i < files.length; ++i) {
      fileItems.push({
        uid: uniqueId('file'),
        source: URL.createObjectURL(files.item(i)),
        file: files.item(i)
      });
    }
    setItems(items => {
      return uniq(concat(items, fileItems), 'uid').filter(Boolean);
    });
  }, [setItems]);

  const allowVideos = useResourceConfig('video', 'video');

  let accept = 'image/*';

  if (allowVideos) {
    accept = 'image/*, video/*';
  }

  return (
    <>
      {variant === 'listing' ? (
        <Button
          color="primary"
          onClick={onClick}
          startIcon={<LineIcon icon="ico-photos-plus-o" />}
          variant="outlined"
        >
          {i18n.formatMessage({ id: 'add_photos' })}
        </Button>
      ) : (
        <Button
          color="primary"
          onClick={onClick}
          variant="outlined"
          startIcon={<LineIcon icon="ico-plus" />}
        >
          {i18n.formatMessage({ id: 'add_photos' })}
        </Button>
      )}
      <input
        onChange={handleChange}
        multiple
        ref={inputRef}
        className="srOnly"
        type="file"
        accept={accept}
      />
    </>
  );
}
