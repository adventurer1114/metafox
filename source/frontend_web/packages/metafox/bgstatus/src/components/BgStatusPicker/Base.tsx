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

export interface BgStatusPickerProps {
  onSelectItem: (item: unknown) => void;
}

export default function BgStatusPicker(props: BgStatusPickerProps) {
  const { useDialog, i18n } = useGlobal();
  const { dialogProps, setDialogValue, closeDialog } = useDialog();
  const { onSelectItem } = props;
  const classes = useStyles();
  const theme = useTheme();
  const isSmallScreen = useMediaQuery(theme.breakpoints.down('sm'));

  const { collections } = useSelector<GlobalState, AppState>(
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

  return (
    <Dialog maxWidth="sm" fullWidth {...dialogProps}>
      <DialogTitle enableBack disableClose>
        {i18n.formatMessage({ id: 'select_background_status' })}
      </DialogTitle>
      <DialogContent variant="fitScroll">
        <ScrollContainer {...scrollProps}>
          <Box sx={{ px: 2 }}>
            {collections.map(data => (
              <Collection
                key={data.id.toString()}
                classes={classes}
                data={data}
                onSelectItem={handleSelect}
              />
            ))}
          </Box>
        </ScrollContainer>
      </DialogContent>
    </Dialog>
  );
}
