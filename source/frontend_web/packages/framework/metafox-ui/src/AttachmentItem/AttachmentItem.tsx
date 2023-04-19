import { useGlobal } from '@metafox/framework';
import { Image, LineIcon, TruncateText } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import clsx from 'clsx';
import React from 'react';
import useStyles from './AttachmentItem.styles';

export type AttachmentItemProps = {
  fileName: string;
  downloadUrl: string;
  isImage: boolean;
  fileSizeText?: string;
  size?: 'small' | 'large' | 'mini';
  image?: Record<number, any>;
};

export default function AttachmentItem(props: AttachmentItemProps) {
  const {
    fileName,
    downloadUrl,
    isImage,
    fileSizeText,
    size = 'small',
    image
  } = props;
  const { dispatch } = useGlobal();
  const classes = useStyles();
  const downloadIconRef = React.useRef<HTMLAnchorElement>();

  const [widthDownIcon, setWidthDownIcon] = React.useState('40px');

  React.useEffect(() => {
    const width =
      downloadIconRef.current &&
      `${downloadIconRef.current.getBoundingClientRect().width}px`;

    setWidthDownIcon(width);
  }, []);

  const icon = isImage ? 'ico-file-photo-o' : 'ico-file-zip-o';
  const photo = getImageSrc(image, '500');

  const presentPhoto = src => {
    dispatch({
      type: 'photo/presentSimplePhoto',
      payload: { src, alt: 'photo' }
    });
  };

  return (
    <div
      className={clsx(
        classes.attachmentWrapper,
        size === 'large' && classes.largeSize,
        size === 'mini' && classes.miniSize
      )}
    >
      <div className={classes.attachmentPhoto}>
        {isImage ? (
          <div onClick={() => presentPhoto(photo)} role="button">
            <Image aspectRatio="11" src={photo} alt={'photo'} />
          </div>
        ) : (
          <LineIcon className={classes.attachmentTypeIcon} icon={icon} />
        )}
      </div>

      <div className={classes.statistic}>
        <TruncateText
          className={classes.fileName}
          lines={1}
          variant="body1"
          sx={{ fontWeight: 600, paddingRight: widthDownIcon }}
        >
          {fileName}
        </TruncateText>
        <div className={classes.fileSize}>{fileSizeText}</div>
      </div>
      <a
        role="button"
        ref={downloadIconRef}
        download={fileName}
        href={downloadUrl}
        className={clsx(
          classes.downloadButton,
          size === 'large' && classes.largeDownloadButton
        )}
      >
        <LineIcon className={classes.downloadIcon} icon="ico-download" />
      </a>
    </div>
  );
}
