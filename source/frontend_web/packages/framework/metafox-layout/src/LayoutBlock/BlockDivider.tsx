import clsx from 'clsx';
import React from 'react';

export default function BlockDivider({ variant }: { variant?: string }) {
  if (!variant || variant === 'none') return null;

  return <div className={clsx(`blockDivider-${variant}`)} />;
}
