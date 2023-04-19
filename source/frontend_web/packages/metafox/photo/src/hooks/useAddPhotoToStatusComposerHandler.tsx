import {
  BasicFileItem,
  StatusComposerRef,
  useGlobal,
  useResourceConfig
} from '@metafox/framework';
import {
  shortenFileName,
  isVideoType,
  parseFileSize,
  isPhotoType
} from '@metafox/utils';
import { concat, get, uniq, uniqueId } from 'lodash';
import React from 'react';
import { isPhoto, isOneTypeMedia } from '../utils';

type FileLimit = File & { maxSize: number };

export default function useAddPhotoToStatusComposerHandler(
  composerRef: React.MutableRefObject<StatusComposerRef>,
  inputRef?: React.MutableRefObject<HTMLInputElement>,
  canUploadTypes?: { photo?: boolean; video?: boolean }
): [(value) => void, () => void] {
  const { dialogBackend, i18n, useLimitFileSize, getAcl, getSetting } =
    useGlobal();
  const MAX_SIZE_FILE = useLimitFileSize();
  const maxSizePhoto = MAX_SIZE_FILE?.photo || MAX_SIZE_FILE?.other || 0;
  const maxSizeVideo = MAX_SIZE_FILE?.video || MAX_SIZE_FILE?.other || 0;
  const allowVideos = useResourceConfig('video', 'video');
  const acl = getAcl();
  const setting = getSetting();
  const allowUploadVideoWithPhoto = get(
    setting,
    'photo.allow_uploading_with_video'
  );
  const limit: number = get(
    acl,
    'photo.photo.maximum_number_of_media_per_upload'
  );
  const canUploadVideo = canUploadTypes
    ? canUploadTypes.video
    : get(acl, 'video.video.create');
  const canUploadPhoto = canUploadTypes
    ? canUploadTypes.photo
    : get(acl, 'photo.photo.create');

  const onClick = React.useCallback(() => {
    inputRef?.current?.click();
  }, [inputRef]);

  const handleChange = React.useCallback(
    (files, clearFile) => {
      if (!files.length) return;

      const filesCurrent: BasicFileItem[] = get(
        composerRef.current.state,
        'attachments.photo.value'
      );

      if (limit > 0) {
        // limit 0 is unlimit
        const totalFileUid = filesCurrent?.length
          ? filesCurrent.filter(x => x.uid !== undefined).length
          : 0;
        const totalFile = files.length + totalFileUid;

        if (totalFile > limit) {
          clearFile();

          return dialogBackend.alert({
            message: i18n.formatMessage(
              { id: 'maximum_per_upload_limit_reached' },
              {
                limit
              }
            )
          });
        }
      }

      let isOnlyVideo = false;
      let isOnlyPhoto = false;
      let isOnlyOneType = false;

      if (!allowUploadVideoWithPhoto) {
        if (filesCurrent?.length) {
          const fileCurrentFirst =
            filesCurrent instanceof FileList
              ? filesCurrent.item(0)
              : filesCurrent[0];

          isOnlyVideo = isVideoType(fileCurrentFirst?.file?.type);
          isOnlyPhoto = !isVideoType(fileCurrentFirst?.file?.type);
        } else {
          isOnlyOneType = true;
        }
      }

      if (!isPhoto(files, Boolean(allowVideos))) {
        return dialogBackend.alert({
          message: i18n.formatMessage({ id: 'cant_add_attachment' })
        });
      }

      if (isOnlyOneType && !isOneTypeMedia(files)) {
        return dialogBackend.alert({
          message: i18n.formatMessage({
            id: 'please_only_upload_one_file_type'
          })
        });
      }

      const { setAttachments, requestComposerUpdate } = composerRef.current;

      if (!setAttachments) return;

      const fileItems: BasicFileItem[] = [];
      const fileLimitItems: FileLimit[] = [];
      const fileCantUploadType: FileLimit[] = [];
      const fileNotAccept: FileLimit[] = [];

      for (let i = 0; i < files.length; ++i) {
        const fileItem = files instanceof FileList ? files.item(i) : files[i];
        const fileItemSize = fileItem.size;

        if (
          (!canUploadVideo && isVideoType(fileItem?.type)) ||
          (!canUploadPhoto && isPhotoType(fileItem?.type))
        ) {
          fileCantUploadType.push(fileItem);
          continue;
        }

        if (!allowUploadVideoWithPhoto) {
          if (
            (isOnlyVideo && !isVideoType(fileItem?.type)) ||
            (isVideoType(fileItem?.type) && isOnlyPhoto)
          ) {
            fileNotAccept.push(fileItem);
            continue;
          }
        }

        const maxSize = isVideoType(fileItem?.type)
          ? maxSizeVideo
          : maxSizePhoto;

        if (fileItemSize > maxSize && maxSize !== 0) {
          fileItem.maxSize = maxSize;
          fileLimitItems.push(fileItem);
        } else
          fileItems.push({
            uid: uniqueId('file'),
            source: URL.createObjectURL(fileItem),
            file: fileItem
          });
      }

      const items = get(composerRef.current.state, 'attachments.photo.value');
      const value = uniq(concat(items, fileItems), 'uid').filter(Boolean);

      value.length > 0 &&
        setAttachments('photo', 'photo', {
          as: 'StatusComposerControlAttachedPhotos',
          value
        });

      if (fileNotAccept.length || fileCantUploadType.length) {
        return dialogBackend
          .alert({
            message: i18n.formatMessage({ id: 'cannot_play_back_the_file' })
          })
          .then(() => {
            if (requestComposerUpdate) {
              requestComposerUpdate();
            }
          });
      }

      if (fileLimitItems.length) {
        return dialogBackend
          .alert({
            message:
              fileLimitItems.length === 1
                ? i18n.formatMessage(
                    { id: 'warning_upload_limit_one_file' },
                    {
                      fileName: shortenFileName(fileLimitItems[0].name, 30),
                      fileSize: parseFileSize(fileLimitItems[0].size),
                      maxSize: parseFileSize(fileLimitItems[0]?.maxSize)
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
          })
          .then(() => {
            if (requestComposerUpdate) {
              requestComposerUpdate();
            }
          });
      }

      if (requestComposerUpdate) {
        setImmediate(() => {
          requestComposerUpdate();
        });
      }

      return null;

      // eslint-disable-next-line react-hooks/exhaustive-deps
    },
    [
      maxSizePhoto,
      maxSizeVideo,
      allowVideos,
      composerRef,
      dialogBackend,
      i18n,
      allowUploadVideoWithPhoto,
      canUploadVideo,
      canUploadPhoto
    ]
  );

  const handleBeforeChange = value => {
    const files = inputRef?.current?.files || value;

    const clear = () => {
      if (inputRef?.current) {
        inputRef.current.value = null;
      }
    };

    handleChange(files, clear);
  };

  return [handleBeforeChange, onClick];
}
