import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { useGlobal, useResourceAction } from '@metafox/framework';
import { APP_PHOTO } from '@metafox/photo/constant';
import { Button, DialogActions, Tab, Tabs } from '@mui/material';
import React from 'react';
import {
  APP_GROUP,
  SELECT_GROUP_PHOTO_TAB,
  SELECT_PROFILE_TAB,
  UPLOAD_TAB
} from './constant';
import SelectPhotoProfile from './SelectPhotoProfile';
import useStyles from './styles';
import Upload from './UpLoad';
import useCheckMediaFileSize from '@metafox/photo/hooks/useCheckMediaFileSize';

export default function ChooseAlbumItemDialog({
  config,
  fileItems,
  setFileItems,
  isDetailAlbum = false,
  formik
}) {
  const { useDialog, i18n, compactData } = useGlobal();
  const { dialogProps, closeDialog } = useDialog();
  const classes = useStyles();
  const {
    dialogTitle,
    max_upload_filesize: maxSizeLimit,
    upload_url,
    isVideoUploadAllowed,
    acceptFail
  } = config || {};

  const [tab, setTab] = React.useState<number>(UPLOAD_TAB);
  const [files, setFiles, handleProcessFiles] = useCheckMediaFileSize({
    initialValues: [],
    upload_url,
    maxSizeLimit,
    isAcceptVideo: isVideoUploadAllowed,
    messageAcceptFail: acceptFail
  });

  const dataSourceProfile = useResourceAction(
    APP_PHOTO,
    'photo_album',
    'selectFromMyPhotos'
  );

  const dataSourceGroupPhoto = useResourceAction(
    APP_PHOTO,
    'photo_album',
    'selectFromGroupPhotos'
  );
  const { apiUrl: apiUrlGroupPhoto, apiParams: apiParamsGroupPhoto } =
    dataSourceGroupPhoto;

  const changeTab = (_, value) => {
    setTab(value);
  };

  const handleAdd = () => {
    setFileItems([...fileItems, ...files]);
    closeDialog();

    if (isDetailAlbum) {
      setImmediate(formik.submitForm);
    }
  };

  return (
    <Dialog
      {...dialogProps}
      maxWidth={false}
      fullWidth
      className={classes.dialog}
    >
      <DialogTitle className={classes.dialogTitle}>
        {dialogTitle || i18n.formatMessage({ id: 'add_photos_video' })}
      </DialogTitle>
      <DialogContent
        className={classes.dialogContent}
        sx={{
          overflow: 'hidden !important'
        }}
      >
        <Tabs
          value={tab}
          onChange={changeTab}
          indicatorColor="primary"
          textColor="primary"
        >
          <Tab
            className={classes.tab}
            disableRipple
            label={i18n.formatMessage({ id: 'upload' })}
          />
          <Tab
            className={classes.tab}
            disableRipple
            label={i18n.formatMessage({ id: 'select_from_my_photos' })}
          />
          {formik && formik?.values?.owner_type === APP_GROUP && (
            <Tab
              className={classes.tab}
              disableRipple
              label={i18n.formatMessage({ id: 'select_from_group_photos' })}
            />
          )}
        </Tabs>
        {tab === UPLOAD_TAB && (
          <Upload
            config={config}
            uploadFiles={files}
            setFiles={setFiles}
            checkFilesSize={handleProcessFiles}
          />
        )}
        {tab === SELECT_PROFILE_TAB && (
          <SelectPhotoProfile
            fileItems={fileItems}
            uploadFiles={files}
            setFiles={setFiles}
            isDetailAlbum={isDetailAlbum}
            albumId={formik?.values?.album}
            dataSource={dataSourceProfile}
          />
        )}
        {tab === SELECT_GROUP_PHOTO_TAB && (
          <SelectPhotoProfile
            fileItems={fileItems}
            uploadFiles={files}
            setFiles={setFiles}
            isDetailAlbum={isDetailAlbum}
            albumId={formik?.values?.album}
            dataSource={{
              apiUrl: apiUrlGroupPhoto,
              apiParams: compactData(apiParamsGroupPhoto, {
                user_id: formik?.values?.owner_id
              })
            }}
          />
        )}
      </DialogContent>
      <DialogActions>
        <Button
          data-testid="buttonSubmit"
          role="button"
          id="buttonSubmit"
          autoFocus
          color="primary"
          size="medium"
          variant="contained"
          onClick={handleAdd}
          sx={{ minWidth: 120 }}
          disabled={files.length ? false : true}
        >
          {i18n.formatMessage({ id: 'add_to_album' })}
        </Button>
      </DialogActions>
    </Dialog>
  );
}
