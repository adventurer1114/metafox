/**
 * @type: service
 * name: CommentActButton
 */
import { useGlobal } from '@metafox/framework';
import { ActButton, ActButtonProps } from '@metafox/ui';
import React from 'react';

export type CommentActButtonProps = {
  handleAction: any;
  onlyIcon?: boolean;
} & Partial<ActButtonProps>;

export default function CommentActButton({
  minimize,
  onlyIcon,
  handleAction
}: CommentActButtonProps) {
  const { i18n } = useGlobal();

  return (
    <ActButton
      data-testid="commentButton"
      minimize={minimize}
      icon="ico-comment-o"
      onClick={() => handleAction('onPressedCommentActButton')}
      label={onlyIcon ? undefined : i18n.formatMessage({ id: 'comment' })}
    />
  );
}
