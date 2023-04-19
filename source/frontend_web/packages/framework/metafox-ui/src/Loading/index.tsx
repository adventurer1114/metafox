/**
 * @type: ui
 * name: Loading
 */
import { styled, useTheme } from '@mui/material/styles';
import React from 'react';
import LoadingSVG from './LoadingSVG.svg';
import LoadingSVGDark from './LoadingSVGDark.svg';

type LoadingVariant = 'absolute' | 'fixed' | 'relative' | undefined;

interface LoadingProps {
  related?: boolean;
  center?: boolean;
  absolute?: boolean;
  variant?: LoadingVariant;
}

const LoadingRoot = styled('div', {
  name: 'Loading',
  slot: 'Root',
  shouldForwardProp: prop =>
    prop !== 'related' && prop !== 'darkMode' && prop !== 'center'
})<{
  darkMode?: boolean;
  variant?: LoadingVariant;
  related?: boolean;

  center?: boolean;
  absolute?: boolean;
}>(({ theme, variant, darkMode }) => ({
  ...(variant === 'absolute' && {
    display: 'flex',
    justifyContent: 'center',
    minHeight: '100%',
    position: 'absolute',
    left: '0',
    top: '0',
    right: '0',
    bottom: '0',
    alignItems: 'center',
    backgroundColor: darkMode ? process.env.MFOX_LOADING_BG : '#fff'
  }),
  ...((variant === 'fixed' || !variant) && {
    display: 'flex',
    justifyContent: 'center',
    minHeight: '100%',
    position: 'fixed',
    left: '0',
    top: '0',
    right: '0',
    bottom: '0',
    alignItems: 'center',
    backgroundColor: darkMode ? process.env.MFOX_LOADING_BG : '#fff',
    zIndex: 1,
    opacity: 0.5
  }),
  ...(variant === 'relative' && {
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: darkMode ? process.env.MFOX_LOADING_BG : '#fff'
  })
}));

export default function Loading({
  related,
  center,
  absolute,
  variant
}: LoadingProps) {
  const theme = useTheme();
  const darkMode = theme.palette.mode === 'dark';

  if (!variant) {
    if (absolute) {
      variant = 'absolute';
    } else if (related && center) {
      variant = 'relative';
    }
  }

  return (
    <LoadingRoot
      variant={variant}
      darkMode={darkMode}
      data-testid="loadingIndicator"
    >
      <div style={{ width: '5rem', height: '5rem' }}>
        {darkMode ? <LoadingSVGDark /> : <LoadingSVG />}
      </div>
    </LoadingRoot>
  );
}
