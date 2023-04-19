import { useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { BackgroundSize, TruncateViewMore } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import * as React from 'react';

export type FeedStatusViewProps = {
  backgroundImage?: Record<BackgroundSize, string>;
  status?: string;
  classes?: Record<string, any>;
  'data-testid'?: string;
};

export default function FeedStatusView({
  status,
  backgroundImage,
  classes,
  'data-testid': testid = 'feed status'
}: FeedStatusViewProps) {
  const { useTheme, useIsMobile } = useGlobal();
  const theme = useTheme();

  const isMobile = useIsMobile();

  if (!status) return null;

  const background = getImageSrc(backgroundImage);

  return background ? (
    <div
      data-testid={testid}
      className={`${classes?.statusRoot} ${classes?.statusBgWrapper} withBackgroundStatus`}
      style={{ backgroundImage: `url(${background})` }}
    >
      <div className={classes?.statusBgInner}>
        <HtmlViewer html={status} />
      </div>
    </div>
  ) : (
    <div className={classes?.statusRoot} data-testid={testid}>
      <TruncateViewMore
        key={status.length.toString()}
        truncateProps={{
          variant: isMobile ? 'subtitle1' : 'body1',
          lines: 3,
          style: { fontWeight: theme.typography.fontWeightRegular }
        }}
      >
        <HtmlViewer html={status} />
      </TruncateViewMore>
    </div>
  );
}
