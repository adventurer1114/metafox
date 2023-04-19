import { Box, Grid } from '@mui/material';
import { styled } from '@mui/material/styles';

export interface SlotProps {
  minHeight?: string;
  maxWidth?: string;
  minWidth?: string;
  flexWeight?: string | number;
  useFlex?: boolean;
  fixed?: boolean;
}

export interface SlotOuterProps {
  minHeight?: string;
  maxWidth?: string;
  minWidth?: string;
  liveEdit?: boolean;
  fixed?: boolean;
  controller?: boolean;
  sticky?: string;
}
export const Slot = styled(Grid, {
  name: 'StyledSlot',
  slot: 'root',
  shouldForwardProp: prop =>
    prop !== 'maxWidth' &&
    prop !== 'minWidth' &&
    prop !== 'minHeight' &&
    prop !== 'flexWeight' &&
    prop !== 'useFlex'
})<SlotProps>(
  ({ theme, maxWidth, minWidth, minHeight, flexWeight, useFlex }) => ({
    display: 'block',
    flexBasis: '100%',
    position: 'relative',
    ...(minHeight === 'screen' && {
      minHeight: '100vh'
    }),
    [theme.breakpoints.down('xl')]: {
      ...(maxWidth && {
        maxWidth: `${theme.layoutSlot.points[maxWidth]}px !important`
      })
    },
    ...(minWidth && {
      minWidth: `${theme.layoutSlot.points[minWidth]}px !important`
    }),
    ...(useFlex && {
      flex: flexWeight ?? 1,
      minWidth: 0
    })
  })
);

export const SlotStage = styled(Box, {
  name: 'StyledSlot',
  slot: 'stage',
  shouldForwardProp: (prop: string) =>
    prop !== 'maxWidth' &&
    prop !== 'minWidth' &&
    prop !== 'minHeight' &&
    prop !== 'controller' &&
    prop !== 'sticky' &&
    prop !== 'liveEdit'
})<SlotOuterProps>(
  ({
    theme,
    minHeight,
    fixed,
    maxWidth,
    minWidth,
    liveEdit,
    controller,
    sticky
  }) => ({
    display: 'block',
    flexBasis: '100%',
    ...(fixed && {
      position: 'fixed'
    }),
    ...(minHeight === 'screen' && {
      minHeight: '100vh'
    }),
    ...(maxWidth && {
      maxWidth: theme.layoutSlot.points[maxWidth]
    }),
    ...(minWidth && {
      maxWidth: theme.layoutSlot.points[minWidth]
    }),
    ...(liveEdit && {
      position: 'relative',
      minHeight: theme.spacing(6)
    }),
    ...(controller && {
      position: 'relative',
      marginBottom: theme.spacing(1),
      padding: theme.spacing(1),
      minHeight: theme.spacing(8),
      borderColor: theme.palette.text.primary,
      borderStyle: 'dotted',
      borderWidth: 1
    }),
    ...(sticky === 'sideStickyStatic' && {
      position: 'sticky',
      top: 0
    }),
    ...(sticky === 'sideSticky' && {
      position: 'sticky'
    })
  })
);

export const PreviewSlot = styled(Box, {
  name: 'StyledSlot',
  slot: 'preview',
  shouldForwardProp: prop => prop !== 'name'
})<{ name?: string }>(({ name, theme }) => ({
  fontSize: '0.8125rem',
  fontWeight: theme.typography.fontWeightMedium,
  height: 80,
  textTransform: 'lowercase',
  alignItems: 'center',
  justifyContent: 'center',
  display: 'flex'
}));

export const SlotContent = styled(Box, {
  name: 'StyledSlot',
  slot: 'content',
  shouldForwardProp: (prop: string) =>
    prop !== 'maxWidth' &&
    prop !== 'minWidth' &&
    prop !== 'minHeight' &&
    prop !== 'fixed'
})<SlotOuterProps>(({ theme, minHeight, maxWidth, minWidth }) => ({
  display: 'block',
  flexBasis: '100%',
  marginLeft: 'auto',
  marginRight: 'auto',
  ...(minHeight === 'screen' && {
    minHeight: '100vh'
  }),
  ...(maxWidth && {
    maxWidth: theme.layoutSlot.points[maxWidth]
  }),
  ...(minWidth && {
    maxWidth: theme.layoutSlot.points[minWidth]
  })
}));

export const StickyBlock = styled('div', {
  name: 'StyledSlot',
  slot: 'stickyRoot'
})({
  display: 'flex',
  height: '100%',
  flexDirection: 'column'
});

export const StickyHeader = styled('div', {
  name: 'StyledSlot',
  slot: 'stickyHeader'
})({});

export const StickyContent = styled('div', {
  name: 'StyledSlot',
  slot: 'stickyContent'
})({
  flex: 1,
  minHeight: 0
});
