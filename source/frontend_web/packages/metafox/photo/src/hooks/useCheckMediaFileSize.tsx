import { useGlobal, BasicFileItem } from '@metafox/framework';
import React, { useEffect } from 'react';
import { isEmpty, uniqueId } from 'lodash';
import { shortenFileName, isVideoType, parseFileSize } from '@metafox/utils';
type Props = {
  initialValues?: BasicFileItem[];
  upload_url?: string;
  maxSizeLimit: Record<string, any>;
  isAcceptVideo?: boolean;
  messageAcceptFail?: string;
};

export default function useCheckFileSize({
  initialValues,
  upload_url = '',
  maxSizeLimit,
  isAcceptVideo = true,
  messageAcceptFail
}: Props) {
  const { dialogBackend, i18n } = useGlobal();
  const mounted = React.useRef<boolean>(true);
  const [processFiles, handleProcessFiles] = React.useState<BasicFileItem[]>(
    []
  );
  const [validFileItems, setValidFileItems] = React.useState<BasicFileItem[]>(
    initialValues || []
  );
  const maxSizePhoto = maxSizeLimit?.photo || maxSizeLimit?.other || 0;
  const maxSizeVideo = maxSizeLimit?.video || maxSizeLimit?.other || 0;

  const handleFiles = files => {
    const newItems = [];

    const fileLimitItems = [];

    for (let index = 0; index < files.length; ++index) {
      const file = files[index];
      const fileSize = file.size;
      const maxSize = isVideoType(file?.type) ? maxSizeVideo : maxSizePhoto;
      const fileItem: BasicFileItem = {
        id: 0,
        uid: uniqueId(),
        source: URL.createObjectURL(file),
        file_name: file.name,
        file_size: file.size,
        file_type: file.type,
        file,
        upload_url,
        type: file.type.match('image/*') ? 'photo' : 'video',
        status: 'create'
      };

      if (fileItem.type === 'video' && !isAcceptVideo) {
        dialogBackend.alert({
          message: messageAcceptFail
        });

        break;
      }

      if (fileSize > maxSize && maxSize !== 0) {
        fileItem.max_size = maxSize;
        fileLimitItems.push(fileItem);
      } else {
        newItems.push(fileItem);
      }
    }

    if (newItems.length) {
      setValidFileItems(prev => [...(prev || []), ...newItems]);
    }

    if (fileLimitItems.length > 0) {
      dialogBackend.alert({
        message:
          fileLimitItems.length === 1
            ? i18n.formatMessage(
                { id: 'warning_upload_limit_one_file' },
                {
                  fileName: shortenFileName(fileLimitItems[0].file_name, 30),
                  fileSize: parseFileSize(fileLimitItems[0].file_size),
                  maxSize: parseFileSize(fileLimitItems[0]?.max_size)
                }
              )
            : i18n.formatMessage(
                { id: 'warning_upload_limit_multi_file' },
                {
                  numberFile: fileLimitItems.length,
                  photoMaxSize: parseFileSize(maxSizePhoto),
                  videoMaxSize: parseFileSize(maxSizeVideo)
                }
              )
      });
    }
  };

  useEffect(() => {
    mounted.current = true;

    if (isEmpty(processFiles)) return;

    handleFiles(processFiles);

    return () => {
      mounted.current = false;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [processFiles]); // check pageParams is actual change

  return [validFileItems, setValidFileItems, handleProcessFiles] as const;
}
