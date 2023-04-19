import React from 'react';
import Flag, { FlagProps } from './Flag/Flag';

export default function PendingFlag({
  variant = 'itemView',
  value
}: Pick<FlagProps, 'variant' | 'value'>) {
  if (!value) return null;

  return (
    <Flag
      data-testid="pending"
      type="is_pending"
      color="white"
      variant={variant}
      value={value}
    />
  );
}
