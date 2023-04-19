import { Theme } from '@mui/material';
import { CSSProperties } from 'react';

export default function border(theme: Theme) {
  return (
    borderColor: 'primary' | 'secondary',
    borderWidth: number = 1,
    borderStyle: CSSProperties['borderStyle'] = 'solid'
  ): CSSProperties['border'] => {
    return `${borderWidth}px ${borderStyle} ${theme.palette.border[borderColor]}`;
  };
}
