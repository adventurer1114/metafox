import {
  BasicFileItem,
  StatusComposerRef,
  useGlobal
} from '@metafox/framework';
import { concat, get, uniq, uniqueId } from 'lodash';
import React from 'react';
import { isLivestream } from '../utils/isLivestream';

export default function useAddLivestreamToStatusComposerHandler(
  composerRef: React.MutableRefObject<StatusComposerRef>,
  inputRef: React.MutableRefObject<HTMLInputElement>
): (() => void)[] {
  const { dialogBackend, i18n } = useGlobal();

  const onClick = React.useCallback(() => {
    inputRef.current.click();
  }, [inputRef]);

  const handleChange = React.useCallback(() => {
    const files = inputRef.current.files;

    if (!files.length) return;

    if (!isLivestream(files)) {
      return dialogBackend.alert({
        message: i18n.formatMessage({ id: 'cant_add_attachment' })
      });
    }

    const { setAttachments, requestComposerUpdate } = composerRef.current;

    if (setAttachments) {
      const fileItems: BasicFileItem[] = [];

      for (let i = 0; i < files.length; ++i) {
        fileItems.push({
          uid: uniqueId('file'),
          source: URL.createObjectURL(files.item(i)),
          file: files.item(i)
        });
      }

      const items = get(
        composerRef.current.state,
        'attachments.livestream.value'
      );
      const value = uniq(concat(items, fileItems)).filter(Boolean);

      setAttachments('livestream', 'livestream', {
        as: 'StatusComposerControlAttatchedLivestreams',
        value
      });

      if (requestComposerUpdate) {
        requestComposerUpdate();
      }
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return [handleChange, onClick];
}
