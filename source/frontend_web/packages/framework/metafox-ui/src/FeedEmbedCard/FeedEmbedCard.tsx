import { FeedEmbedCardProps } from '@metafox/ui';
import clsx from 'clsx';
import * as React from 'react';
import useStyles from './styles';

export default function FeedEmbedCard({
  variant = 'list',
  children
}: FeedEmbedCardProps) {
  const classes = useStyles();
  const bottomSpacing = 'dense';

  return (
    <div
      className={clsx(
        classes.root,
        classes[variant],
        bottomSpacing && classes[`bottomSpacing-${bottomSpacing}`]
      )}
    >
      <div className={classes.itemOuter}>{children}</div>
    </div>
  );
}
