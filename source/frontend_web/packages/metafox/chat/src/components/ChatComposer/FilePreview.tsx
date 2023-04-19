import { PreviewUploadFileHandle } from '@metafox/chat/types';
import { RefOf } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { styled } from '@mui/material';
import React from 'react';
import FilePreviewItem from './FilePreviewItem';

const Root = styled('div', {
  shouldForwardProp: props => props !== 'isAllPage'
})<{ isAllPage?: boolean }>(({ theme, isAllPage }) => ({
  background: theme.palette.background.paper,
  padding: theme.spacing(2, 1, 0, 1),
  display: 'flex',
  overflow: 'hidden',
  borderTop: theme.mixins.border('secondary'),
  userSelect: 'none',
  height: '85px',
  minHeight: '85px',
  ...(isAllPage && {
    height: '90px',
    minHeight: '90px'
  })
}));

const WrapperFile = styled('div')(({ theme }) => ({
  display: 'flex',
  overflowX: 'auto',
  paddingTop: theme.spacing(1),
  marginBottom: theme.spacing(0.5),
  paddingBottom: theme.spacing(0.5),
  '&::-webkit-scrollbar': {
    height: '5px',
    width: '5px',
    background: 'transparent',
    borderRadius: theme.spacing(0.5),
    transition: 'opacity 200ms'
  },

  /* Track */
  '&::-webkit-scrollbar-track': {
    margin: theme.spacing(0.5, 0),
    borderRadius: theme.spacing(0.5),
    background:
      theme.palette.mode === 'light' ? 'white' : theme.palette.grey['300']
  },

  /* Handle */
  '&::-webkit-scrollbar-thumb': {
    background: theme.palette.grey['600'],
    borderRadius: theme.spacing(0.5)
  },

  /* Handle on hover */
  '&::-webkit-scrollbar-thumb:hover': {
    background: theme.palette.grey['700']
  },

  '&::-webkit-scrollbar-thumb:horizontal': {
    background: theme.palette.grey['700'],
    borderRadius: '10px'
  }
}));

const StyledAddFile = styled('div', { slot: 'ButtonAdd' })(({ theme }) => ({
  marginRight: theme.spacing(1),
  cursor: 'pointer',
  span: {
    background:
      theme.palette.mode === 'light'
        ? theme.palette.background.default
        : theme.palette.grey['500'],
    '&:hover': {
      background: theme.palette.action.hover
    },
    borderRadius: theme.spacing(1),
    maxWidth: '52px',
    width: '52px',
    height: '52px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontSize: theme.spacing(2.5)
  }
}));

interface Props {
  files?: File[];
  onChange?: (temp_file: number) => void;
  filesUploadRef?: any;
  isAllPage?: boolean;
}

function PreviewUploadFile(
  { filesUploadRef, isAllPage }: Props,
  ref: RefOf<PreviewUploadFileHandle>
) {
  const [listFiles, setListFiles] = React.useState<File[]>([]);

  const [typeUpload, setTypeUpload] = React.useState('image');

  const inputRef = React.useRef<HTMLInputElement>();
  const fileUploadRef = React.useRef<HTMLInputElement>();

  const removeItem = index => {
    const filesList = [...listFiles];

    if (index > -1) {
      filesList.splice(index, 1);
      setListFiles([...filesList]);

      if (filesUploadRef) {
        filesUploadRef.current?.removeFile(index);
      }
    }
  };

  React.useImperativeHandle(ref, () => {
    return {
      attachFiles: (files: File[]) => {
        if (files?.length) {
          setListFiles(files);
        }
      },
      typeUpload: type => {
        if (type) {
          setTypeUpload(type);
        }
      },
      clear: () => {
        setListFiles([]);
      },
      checkIsLoading: () => {}
    };
  });

  if (!listFiles || !listFiles?.length) return null;

  const attachImages = () => {
    inputRef.current.click();
  };

  const attachFile = () => {
    fileUploadRef.current.click();
  };

  const addFile = () => {
    if (typeUpload === 'image') {
      attachImages();

      return;
    }

    attachFile();
  };

  const onChangeImage = () => {
    if (!inputRef.current.files.length) return;

    const data = [...listFiles, ...inputRef.current.files];
    setListFiles(data);

    if (filesUploadRef) {
      filesUploadRef.current?.attachFiles(data);
    }
  };

  const fileUploadChanged = () => {
    if (!fileUploadRef.current.files.length) return;

    const data = [...listFiles, ...fileUploadRef.current.files];
    setListFiles(data);

    if (filesUploadRef) {
      filesUploadRef.current?.attachFiles(data);
    }
  };

  return (
    <Root isAllPage={isAllPage}>
      <WrapperFile>
        {Object.values(listFiles).map((item, index) => (
          <FilePreviewItem
            key={index}
            file={item}
            onRemove={() => removeItem(index)}
            isAllPage={isAllPage}
          />
        ))}
        <StyledAddFile onClick={addFile}>
          <span>
            <LineIcon icon="ico-text-file-plus" />
          </span>
        </StyledAddFile>
      </WrapperFile>
      <input
        data-testid="inputAttachPhoto"
        onChange={onChangeImage}
        multiple
        ref={inputRef}
        style={{ display: 'none' }}
        type="file"
        accept="image/*"
      />
      <input
        style={{ display: 'none' }}
        type="file"
        multiple
        ref={fileUploadRef}
        onChange={fileUploadChanged}
      />
    </Root>
  );
}

export default React.forwardRef<PreviewUploadFileHandle, Props>(
  PreviewUploadFile
);
