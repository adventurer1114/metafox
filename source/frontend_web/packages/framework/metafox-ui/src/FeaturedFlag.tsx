import React from 'react';
import Flag, { FlagProps } from './Flag';

export default function FeaturedFlag({
  variant = 'itemView',
  value,
  color = 'white'
}: Pick<FlagProps, 'variant' | 'value' | 'color'>) {
  if (!value) return null;

  return (
    <Flag
      data-testid="featured"
      type="is_featured"
      color={color}
      variant={variant}
      value={value}
    />
  );
}
