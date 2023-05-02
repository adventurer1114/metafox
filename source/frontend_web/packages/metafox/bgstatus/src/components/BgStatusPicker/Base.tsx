import { GlobalState, useGlobal } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { ScrollContainer } from '@metafox/layout';
import { getBgStatusSelector } from '@metafox/bgstatus/selectors';
import { Box, useMediaQuery } from '@mui/material';
import { useTheme } from '@mui/material/styles';
import React from 'react';
import { useSelector } from 'react-redux';
import { AppState } from '../../types';
import Collection from './BgStatusCollection';
import useStyles from './styles';
import BgStatusSkeleton from './BgStatusSkeleton';

export interface BgStatusPickerProps {
  onSelectItem: (item: unknown) => void;
  selectedId?: number;
}

export default function BgStatusPicker(props: BgStatusPickerProps) {
  const { useDialog, i18n } = useGlobal();
  const { dialogProps, setDialogValue, closeDialog } = useDialog();
  const { onSelectItem, selectedId } = props;
  const classes = useStyles();
  const theme = useTheme();
  const isSmallScreen = useMediaQuery(theme.breakpoints.down('sm'));

  const { collections, loaded } = useSelector<GlobalState, AppState>(
    getBgStatusSelector
  );

  const handleSelect = (item: unknown) => {
    if (onSelectItem) {
      onSelectItem(item);
    }

    setDialogValue(item);
    closeDialog();
  };

  const scrollProps = isSmallScreen ? { autoHeightMax: 'none' } : {};

  if (!loaded) {
    return (
      <Dialog maxWidth="sm" fullWidth {...dialogProps}>
        <DialogTitle enableBack disableClose>
          {i18n.formatMessage({ id: 'select_background_status' })}
        </DialogTitle>
        <DialogContent variant="fitScroll">
          <ScrollContainer {...scrollProps}>
            <Box sx={{ p: 2 }}>
              <BgStatusSkeleton />
            </Box>
          </ScrollContainer>
        </DialogContent>
      </Dialog>
    );
  }

  return (
    <Dialog maxWidth="sm" fullWidth {...dialogProps}>
      <DialogTitle enableBack disableClose>
        {i18n.formatMessage({ id: 'select_background_status' })}
      </DialogTitle>
      <DialogContent variant="fitScroll">
        <ScrollContainer {...scrollProps}>
          <Box sx={{ p: 2 }}>
            {collections.map(data => (
              <Collection
                key={data.id.toString()}
                classes={classes}
                data={data}
                onSelectItem={handleSelect}
                selectedId={selectedId}
              />
            ))}
          </Box>
        </ScrollContainer>
      </DialogContent>
    </Dialog>
  );
}
