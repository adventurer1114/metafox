/**
 * @type: block
 * name: livestreaming.block.commentLiveListing
 * title: livestream detail comment
 * keywords: livestream
 */
import {
  connectItem,
  useGlobal,
  useFirestoreDocIdListener
} from '@metafox/framework';
import * as React from 'react';
import CommentListLiveStream from './CommentList';

type Props = {
  streamKey: string;
  identity: string;
  scrollToBottom: () => void;
  setParentReply: () => void;
};

function LivestreamComment({
  streamKey,
  identity,
  scrollToBottom,
  setParentReply
}: Props) {
  const { firebaseBackend, dispatch } = useGlobal();

  const db = firebaseBackend.getFirestore();
  const obsConnected = useFirestoreDocIdListener(db, {
    collection: 'live_video_comment',
    docID: streamKey
  });

  const totalComment = obsConnected?.total_comment;

  React.useEffect(() => {
    dispatch({
      type: 'livestreaming/updateStatistic',
      payload: {
        identity,
        statistic: { total_comment: totalComment }
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [totalComment]);

  React.useEffect(() => {
    dispatch({
      type: 'livestreaming/updateComment',
      payload: {
        data: obsConnected?.comment
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [obsConnected?.comment]);

  if (!streamKey || !obsConnected) return null;

  const dataComment = obsConnected?.comment.slice(
    Math.max(obsConnected?.comment.length - 20, 0)
  );

  return (
    <CommentListLiveStream
      data={dataComment}
      identity={identity}
      scrollToBottom={scrollToBottom}
      setParentReply={setParentReply}
    />
  );
}

export default connectItem(LivestreamComment);
