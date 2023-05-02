/**
 * @type: formElement
 * name: form.element.MultiAudioField
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { DropFileBox, InputNotched, LineIcon } from '@metafox/ui';
import { parseFileSize, shortenFileName } from '@metafox/utils';
import {
  Box,
  Button,
  FormControl,
  Typography,
  TextField,
  InputLabel
} from '@mui/material';
import MuiTextField from '@mui/material/TextField';
import { styled } from '@mui/material/styles';
import { useField } from 'formik';
import produce from 'immer';
import { camelCase, uniqueId, debounce } from 'lodash';
import React, { useRef } from 'react';
import ErrorMessage from '../ErrorMessage';

export interface DropButtonProps {
  isOver?: boolean;
}

const DropButton = styled(Button, {
  name: 'DropButton',
  slot: 'DropButton'
})<DropButtonProps>(({ theme, isOver }) => ({
  ...(isOver && {
    backgroundColor: theme.palette.action.hover
  })
}));

const AudioUploaded = styled('div', {
  name: 'AudioUploaded'
})<{}>(({ theme }) => ({
  display: 'flex',
  fontSize: theme.mixins.pxToRem(13),
  justifyContent: 'space-between',
  paddingBottom: theme.spacing(1),
  '& .ico-music-note-o': {
    fontSize: theme.mixins.pxToRem(15)
  }
}));

const EditAudio = styled('div', {
  name: 'EditAudio'
})<{}>(({ theme }) => ({
  padding: theme.spacing(2),
  marginBottom: theme.spacing(1.5),
  border: '1px solid #eeeeee',
  '& > div': {
    width: '100%'
  }
}));

const IconAction = styled('div', {
  name: 'IconAction'
})<{}>(({ theme }) => ({
  display: 'flex',
  '& span': {
    marginLeft: theme.spacing(1)
  }
}));

const DropzoneBox = styled('div', {
  name: 'DropzoneBox'
})<{}>(({ theme }) => ({
  width: 'fit-content'
}));

const Label = styled(InputLabel, {
  name: 'Label'
})<{ haveError?: boolean }>(({ theme, haveError }) => ({
  ...(haveError && {
    color: theme.palette.error.main
  })
}));

function AudioItem({
  item,
  index,
  handleChange,
  showEditAudio,
  handleEditAudio,
  handleRemoveAudio,
  maxLengthName
}) {
  // does not render item is removed
  // keep item to send to server
  if (item.status === 'remove') return null;

  return (
    <>
      <AudioUploaded>
        <Box sx={{ display: 'flex' }}>
          <LineIcon icon="ico-music-note-o" />
          <Box ml={1} mr={1}>
            {shortenFileName(item?.file_name, 70)}
          </Box>
        </Box>
        <IconAction>
          <Typography
            variant="body2"
            color="primary"
            component="span"
            role="button"
            onClick={() => handleEditAudio(item?.id || item?.uid)}
          >
            <LineIcon icon="ico-compose" />
          </Typography>
          <Typography
            variant="body2"
            color="primary"
            component="span"
            role="button"
            onClick={() => handleRemoveAudio(index)}
          >
            <LineIcon icon="ico-close" />
          </Typography>
        </IconAction>
      </AudioUploaded>
      {showEditAudio === (item?.id || item?.uid) ? (
        <EditAudio>
          <TextField
            placeholder="Fill in a title for your song"
            required
            label="Name"
            name="name"
            variant="outlined"
            size="small"
            id="outlined-required"
            defaultValue={item?.name}
            onChange={e =>
              handleChange(e.currentTarget.value, index, e.currentTarget.name)
            }
            inputProps={{ maxLength: maxLengthName }}
          />
          <Box mt={2} />
          <MuiTextField
            rows={3}
            placeholder="Add some description to your song"
            id="outlined-basic"
            label="Description"
            name="description"
            variant="outlined"
            size="small"
            defaultValue={item?.description}
            multiline
            onChange={e =>
              handleChange(e.currentTarget.value, index, e.currentTarget.name)
            }
          />
        </EditAudio>
      ) : null}
    </>
  );
}

export default function MultiAudioField({
  name,
  config,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    label = 'Select songs',
    description,
    item_type,
    accept = 'audio/mp3',
    max_upload_filesize,
    max_length_name = 100,
    upload_url,
    disabled,
    storage_id
  } = config;
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const { dialogBackend, i18n } = useGlobal();
  const [field, meta, { setValue, setTouched }] = useField(
    name ?? 'ItemPhotoField'
  );
  const [showAudioName, setShowAudioName] = React.useState<boolean>(false);
  const [showEditAudio, setShowEditAudio] = React.useState<string>();
  const inputRef = useRef<HTMLInputElement>();

  const [listAudio, setListAudio] = React.useState<BasicFileItem[]>(
    field.value || []
  );

  const handleControlClick = () => {
    inputRef.current.click();
  };

  const handleRemoveAudio = React.useCallback(
    (index: number) => {
      dialogBackend
        .confirm({
          message: i18n.formatMessage({
            id: 'are_you_sure_you_want_to_delete_attachment_file'
          }),
          title: i18n.formatMessage({ id: 'are_you_sure' })
        })
        .then(oke => {
          if (!oke) return;

          setListAudio(prev =>
            produce(prev, draft => {
              if (draft[index].id) {
                draft[index].status = 'remove';
              } else {
                draft.splice(index, 1);
              }
            })
          );
        });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const handleEditAudio = React.useCallback(
    (uid: any) => {
      if (showEditAudio !== uid) {
        setShowEditAudio(uid);
      } else {
        setShowEditAudio(null);
      }
    },
    [showEditAudio]
  );

  React.useEffect(() => {
    if (listAudio.length === 0) {
      setShowAudioName(false);
    }

    setValue(listAudio);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [listAudio]);

  const handleFiles = files => {
    const fileItems: BasicFileItem[] = [];

    for (const index of Object.keys(files)) {
      const itemResult: BasicFileItem = {
        id: 0,
        status: 'create',
        upload_url,
        name: files[index].name.split('.')[0].slice(0, max_length_name),
        description: '',
        source: URL.createObjectURL(files[index]),
        file: files[index],
        file_name: files[index].name,
        file_size: files[index].size,
        file_type: files[index].type,
        uid: uniqueId('file'),
        fileItemType: item_type,
        storage_id: storage_id ?? null
      };

      const fileItemSize = itemResult?.file_size;

      if (
        fileItemSize > max_upload_filesize?.music &&
        max_upload_filesize?.music
      ) {
        dialogBackend.alert({
          message: i18n.formatMessage(
            { id: 'warning_upload_limit_one_file' },
            {
              fileName: shortenFileName(itemResult.file_name, 30),
              fileSize: parseFileSize(itemResult.file_size),
              maxSize: parseFileSize(max_upload_filesize?.music)
            }
          )
        });

        return;
      }

      fileItems.push(itemResult);
    }

    if (fileItems.length) {
      setListAudio(
        produce(draft => {
          draft.push(...fileItems);
        })
      );
    }

    setShowAudioName(true);
  };

  const handleInputChange = React.useCallback(() => {
    const files = inputRef.current.files;

    if (!files) return;

    setTouched(true);
    handleFiles(files);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [listAudio]);

  const onDnDFile = files => {
    if (!files) return;

    handleFiles(files);
  };

  const debounce_fun = debounce((index, val, name) => {
    setListAudio(
      produce(draft => {
        if (draft[index].id) {
          draft[index].status = 'update';
        }

        draft[index][name] = val;
      })
    );
  }, 1000);

  const handleChange = (val: string, index: number, name: string) => {
    debounce_fun(index, val, name);
  };

  const handleResetValue = (
    event: React.MouseEvent<HTMLInputElement, MouseEvent>
  ) => {
    event.currentTarget.value = null;
  };

  const haveErr = !!(meta.error && (meta.touched || formik.submitCount));

  return (
    <FormControl
      fullWidth
      margin="normal"
      data-testid={camelCase(`field ${name}`)}
    >
      <Label haveError={haveErr} required variant="outlined" shrink="true">
        {config.label}
      </Label>
      <Box sx={{ p: 2, pt: 2.5 }}>
        <Typography
          variant="body2"
          color="text.secondary"
          mb={1.5}
          component="p"
        >
          {showAudioName
            ? i18n.formatMessage({ id: 'audio_file_selected' })
            : description}
        </Typography>
        <DropzoneBox>
          <DropFileBox
            onDrop={files => onDnDFile(files)}
            render={({ isOver }) => (
              <DropButton
                onClick={handleControlClick}
                size="small"
                color="primary"
                sx={{ fontWeight: 'bold' }}
                isOver={isOver}
                variant="outlined"
                disabled={disabled || forceDisabled || formik.isSubmitting}
                data-testid={camelCase(`button ${name}`)}
                startIcon={<LineIcon icon="ico-upload" />}
              >
                {label}
              </DropButton>
            )}
          />
        </DropzoneBox>
        {listAudio.length > 0 && (
          <Box sx={{ mt: 2 }}>
            {listAudio.map((item, index) => (
              <AudioItem
                key={index.toString()}
                handleRemoveAudio={handleRemoveAudio}
                handleChange={handleChange}
                handleEditAudio={handleEditAudio}
                showEditAudio={showEditAudio}
                index={index}
                item={item}
                maxLengthName={max_length_name}
              />
            ))}
          </Box>
        )}
        {haveErr ? <ErrorMessage error={meta.error} /> : null}
      </Box>
      <InputNotched
        haveError={haveErr}
        children={config.label}
        variant="outlined"
      />
      <input
        ref={inputRef}
        onClick={handleResetValue}
        className="srOnly"
        type="file"
        data-testid={camelCase(`input ${name}`)}
        multiple
        accept={accept}
        onChange={handleInputChange}
      />
    </FormControl>
  );
}
