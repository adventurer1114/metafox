import React from 'react';
import { ReactionIconProps } from '../types';

export default function ReactionIcon({
  classes,
  src,
  icon,
  title
}: ReactionIconProps) {
  return (
    <span role="button">
      <img src={src || icon} alt={title} />
    </span>
  );
}
