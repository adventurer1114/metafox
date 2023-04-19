import { LineIcon } from '@metafox/ui';
import clsx from 'clsx';
import React from 'react';

interface Props {
  icon: string;
  value?: number;
  label: string;
  onClick?: () => void;
  style?: React.CSSProperties;
  index: number;
  classes: Record<string, string>;
}

export default function ItemStatsItemView({
  icon,
  value,
  label,
  index,
  onClick,
  style,
  classes
}: Props) {
  return (
    <div className={classes.item} style={style} onClick={onClick}>
      <LineIcon
        icon={icon}
        className={clsx(classes.icon, classes[`item${index}`])}
      />
      <span className={classes.value}>{value}</span>
      <span className={classes.label}>{label}</span>
    </div>
  );
}
