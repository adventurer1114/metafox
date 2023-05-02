import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { TabContext, TabList, TabPanel } from '@mui/lab';
import {
  Dialog,
  DialogContent,
  DialogTitle,
  IconButton,
  styled,
  Tab
} from '@mui/material';
import React from 'react';
import UploadForm from './UploadForm';
import UrlForm from './UrlForm';

const tabPaneStyle = { px: 0, py: 1 };

const CloseButton = styled(IconButton, { name: 'MuiDialogClose' })(() => ({
  marginLeft: 'auto',
  transform: 'translate(4px,0)'
}));

function ImagePickerModal({ onExited, onChange }) {
  const [open, setOpen] = React.useState(true);
  const { i18n, dialogBackend } = useGlobal();
  const [tabId, setTabId] = React.useState('1');

  const [haveData, setHaveData] = React.useState<boolean>(false);

  const handleChangeTab = (evt, newValue) => setTabId(newValue);

  const onSubmit = data => {
    onChange(data);
    setOpen(false);
  };

  const stopEvent = evt => {
    evt.stopPropagation();
  };

  const handleClose = async () => {
    if (!haveData) {
      onChange(null);
      setOpen(false);

      return;
    }

    const ok = await dialogBackend.confirm({
      message: i18n.formatMessage({
        id: 'the_change_you_made_will_not_be_saved'
      }),
      title: i18n.formatMessage({
        id: 'unsaved_changes'
      })
    });

    if (ok) {
      onChange(null);
      setOpen(false);
    }
  };

  const handleChange = ({ values }) => {
    setHaveData(Boolean(values?.src));
  };

  return (
    <TabContext value={tabId}>
      <Dialog
        onClick={stopEvent}
        open={open}
        maxWidth="sm"
        fullWidth
        TransitionProps={{
          onExited
        }}
        onClose={handleClose}
        aria-labelledby="modal-modal-title"
        aria-describedby="modal-modal-description"
      >
        <DialogTitle sx={{ minHeight: 'auto' }}>
          <TabList onChange={handleChangeTab} aria-label="Images">
            <Tab label={i18n.formatMessage({ id: 'upload' })} value="1" />
            <Tab
              label={i18n.formatMessage({ id: 'external_image' })}
              value="2"
            />
          </TabList>
          <CloseButton
            size="small"
            onClick={handleClose}
            data-testid="buttonClose"
            role="button"
          >
            <LineIcon icon="ico-close" />
          </CloseButton>
        </DialogTitle>
        <DialogContent>
          <TabPanel sx={tabPaneStyle} value="1">
            <UploadForm onChange={handleChange} onSubmit={onSubmit} />
          </TabPanel>
          <TabPanel sx={tabPaneStyle} value="2">
            <UrlForm onChange={handleChange} onSubmit={onSubmit} />
          </TabPanel>
        </DialogContent>
      </Dialog>
    </TabContext>
  );
}

export default ImagePickerModal;
