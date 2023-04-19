import { connectItem, useGlobal } from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import VideoPlayer from '@metafox/ui/VideoPlayer';
import * as React from 'react';
import { VideoItemShapeDialogProps } from '../../types';
import useStyles from './styles';

function VideoDetail({ item, identity }: VideoItemShapeDialogProps) {
  const classes = useStyles();
  const { ItemDetailInteractionInModal } = useGlobal();

  if (!item) return null;

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <div className={classes.root}>
          <div className={classes.dialogVideo}>
            {item.destination ? (
              <VideoPlayer src={item.destination} thumb_url={item.image} />
            ) : null}
            {item.video_url ? (
              <VideoPlayer src={item.video_url} thumb_url={item.image} />
            ) : null}
          </div>
          <div className={classes.dialogStatistic}>
            <ItemDetailInteractionInModal identity={identity} />
          </div>
        </div>
      </BlockContent>
    </Block>
  );
}

export default connectItem(VideoDetail);
