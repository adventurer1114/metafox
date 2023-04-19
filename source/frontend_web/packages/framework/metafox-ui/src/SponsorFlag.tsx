import React from 'react';
import Flag, { FlagProps } from './Flag/Flag';

export default function SponsorFlag({
  variant = 'itemView',
  value,
  color = 'white'
}: Pick<FlagProps, 'variant' | 'value' | 'color'>) {
  if (!value) return null;

  return (
    <Flag
      data-testid="sponsor"
      type="is_sponsor"
      color={color}
      variant={variant}
      value={value}
    />
  );
}
