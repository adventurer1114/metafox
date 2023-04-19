import { getImageSrc } from '@metafox/utils';
import clsx from 'clsx';
import React from 'react';
import { BgStatusItemShape } from '../../types';

type ClassesKey = 'itemRoot' | 'itemLabel' | 'itemBg';

export type BgStatusItemProps = {
  item: BgStatusItemShape;
  classes: Record<ClassesKey, string>;
  onClick: () => void;
};

export default function BgStatusItem({
  item,
  classes,
  onClick
}: BgStatusItemProps) {
  return (
    <div
      data-testid="itemBackgroundStatus"
      className={classes.itemRoot}
      onClick={onClick}
    >
      <div
        className={clsx(classes.itemBg, 'withBackgroundStatus')}
        style={{
          backgroundImage: `url("${getImageSrc(item.image, '300')}")`
        }}
      ></div>
    </div>
  );
}
