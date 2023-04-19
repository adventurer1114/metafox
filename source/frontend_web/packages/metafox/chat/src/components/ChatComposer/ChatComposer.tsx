/**
 * @type: ui
 * name: ChatSimpleComposer
 * chunkName: composer
 */
import Editor from '@draft-js-plugins/editor';
import { ChatComposerProps } from '@metafox/chat/types';
import { RefOf, useDraftEditorConfig, useGlobal } from '@metafox/framework';
import { Image, LineIcon } from '@metafox/ui';
import { htmlToText, isPhotoType, textToRaw } from '@metafox/utils';
import { IconButton, Tooltip } from '@mui/material';
import clsx from 'clsx';
import {
  ContentState,
  convertFromRaw,
  EditorState,
  getDefaultKeyBinding,
  Modifier,
  SelectionState
} from 'draft-js';
import React, { useEffect } from 'react';
import composerConfig from './composerConfig';
import useStyles from './styles';
import ComposerControl from '../ChatComposerControl';

interface HandleComposer {}

function ChatComposer(
  {
    rid,
    msgId,
    text = '',
    focus,
    reactMode,
    onCancel,
    extra_data,
    onSuccess,
    onMarkAsRead,
    margin = 'normal',
    previewRef
  }: ChatComposerProps,
  ref: RefOf<HandleComposer>
) {
  const classes = useStyles();
  const { i18n, dispatch, jsxBackend } = useGlobal();

  const [editorState, setEditorState] = React.useState<EditorState>(
    EditorState.createWithContent(
      convertFromRaw(textToRaw(htmlToText(text || '')))
    )
  );
  const [stickerId, setStickerId] = React.useState<number>(
    'sticker' === extra_data?.extra_type ? extra_data.extra_id : undefined
  );
  const [photoId, setPhotoId] = React.useState<number>(
    'photo' === extra_data?.extra_type ? extra_data.extra_id : undefined
  );
  const [submitting, setSubmitting] = React.useState<boolean>(false);
  const full_path = extra_data?.full_path;

  const editorRef = React.useRef<Editor>();

  const [previewFiles, setPreviewFiles] = React.useState([]);

  const moveSelectionToEnd = editorState => {
    const content = editorState.getCurrentContent();
    const blockMap = content.getBlockMap();

    const key = blockMap.last().getKey();
    const length = blockMap.last().getLength();

    // On Chrome and Safari, calling focus on contenteditable focuses the
    // cursor at the first character. This is something you don't expect when
    // you're clicking on an input element but not directly on a character.
    // Put the cursor back where it was before the blur.
    const selection = new SelectionState({
      anchorKey: key,
      anchorOffset: length,
      focusKey: key,
      focusOffset: length
    });

    return selection;
  };

  React.useEffect(() => {
    if (reactMode === 'edit') {
      const newState = ContentState.createFromText(text);
      const newEditor = EditorState.createWithContent(newState);

      setEditorState(
        EditorState.forceSelection(newEditor, moveSelectionToEnd(newEditor))
      );
      setImmediate(() => {
        focusEditor();
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [text, reactMode, rid]);

  const clearEditor = React.useCallback(() => {
    const newEditorState = editorState;
    const contentState = newEditorState.getCurrentContent();
    const firstBlock = contentState.getFirstBlock();
    const lastBlock = contentState.getLastBlock();
    const allSelected = new SelectionState({
      anchorKey: firstBlock.getKey(),
      anchorOffset: 0,
      focusKey: lastBlock.getKey(),
      focusOffset: lastBlock.getLength(),
      hasFocus: true
    });
    const clearEditor = EditorState.push(
      newEditorState,
      Modifier.removeRange(contentState, allSelected, 'backward'),
      'remove-range'
    );
    setEditorState(clearEditor);
  }, [editorState]);

  const meta = React.useMemo(() => {
    return {
      onSuccess: () => {
        clearEditor();
        setSubmitting(false);
        typeof onSuccess === 'function' && onSuccess();
      }
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [clearEditor]);

  const condition = React.useMemo(() => {
    return {
      editing: reactMode === 'edit',
      previewFiles: previewFiles.length
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [reactMode, previewFiles]);

  const [editorPlugins, editorComponents, editorControls] =
    useDraftEditorConfig(composerConfig, condition);
  const placeholder = i18n.formatMessage({ id: 'write_message' });

  const onStickerClick = (sticker_id: unknown) => {
    dispatch({
      type: 'chat/composer/SUBMIT',
      payload: { rid, sticker_id },
      meta
    });
  };

  const handleUploadSuccess = () => {
    previewRef.current?.clear();
    setPreviewFiles([]);
    clearEditor();
    setSubmitting(false);

    if (reactMode !== 'no_react') {
      onSuccess();
    }
  };

  const handleUploadFail = () => {
    setSubmitting(false);
  };

  const focusEditor = React.useCallback(() => {
    onMarkAsRead && onMarkAsRead();
    // updating open and focus at the same time cause bug: plugin editor not works
    setImmediate(() => editorRef.current.focus());
  }, [rid]);

  const handleSubmit = () => {
    const text = editorState.getCurrentContent().getPlainText();

    if (!text || !text.trim() || submitting) return;

    setSubmitting(true);

    dispatch({
      type: 'chat/composer/SUBMIT',
      payload: {
        reactMode,
        rid,
        msgId,
        text: text.trim(),
        sticker_id: stickerId,
        photo_id: photoId
      },
      meta
    });
  };

  const keyBindingFn = (evt: any) => {
    if (
      !evt.keyCode ||
      13 !== evt.keyCode ||
      evt.metaKey ||
      evt.shiftKey ||
      evt.altKey ||
      evt.ctrlKey
    ) {
      return getDefaultKeyBinding(evt);
    } else {
      return 'composer-submit';
    }
  };

  const handleKeyCommand = (command: string) => {
    if ('composer-submit' === command) {
      if (
        previewFiles &&
        previewFiles.length &&
        Object.values(previewFiles).length
      ) {
        const text = editorState.getCurrentContent().getPlainText();

        if (submitting) return;

        setSubmitting(true);

        dispatch({
          type: 'chat/composer/upload',
          payload: {
            files: Object.values(previewFiles),
            rid,
            text,
            reactMode,
            msgId
          },
          meta: {
            onSuccess: handleUploadSuccess,
            onFailure: handleUploadFail
          }
        });
      } else {
        handleSubmit();
      }

      return 'handled';
    }

    return 'not-handled';
  };

  useEffect(() => {
    if (focus) {
      focusEditor();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [focus, focusEditor]);

  useEffect(() => {
    const handleESC = event => {
      if (onCancel && 27 === event.keyCode) {
        onCancel();
      }
    };

    window.addEventListener('keydown', handleESC);

    return () => {
      window.removeEventListener('keydown', handleESC);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  React.useImperativeHandle(ref, () => {
    return {
      attachFiles: (files: File[]) => {
        if (files?.length) {
          setPreviewFiles(files);
        }
      },
      removeFile: (index: any) => {
        const filesList = [...previewFiles];

        if (index > -1) {
          filesList.splice(index, 1);

          setPreviewFiles([...filesList]);
        }
      }
    };
  });

  const disableSubmit = React.useMemo(() => {
    const text = editorState.getCurrentContent().getPlainText();
    const data = text && text.trim();

    return !!((!submitting && previewFiles?.length) || data);
  }, [previewFiles, editorState, submitting]);

  const handlePastedFiles = (files: FileList[]) => {
    if (!files.length) return;

    // accept image
    if (previewRef && previewRef.current) {
      const file = files[0];

      if (isPhotoType(file.type)) {
        setPreviewFiles([...previewFiles, file]);
        previewRef.current?.attachFiles([...previewFiles, file]);
      }
    }
  };

  return (
    <form
      className={classes.root}
      role="presentation"
      data-testid="chatComposerForm"
    >
      <div
        className={clsx(
          classes.composeOuter,
          classes[`composeOuter-${margin}`]
        )}
      >
        <div className={classes.composeInner}>
          <div className={classes.composeInputWrapper}>
            <div
              className={classes.composer}
              onClick={focusEditor}
              data-testid="draftEditor"
            >
              <Editor
                stripPastedStyles
                ref={editorRef}
                plugins={editorPlugins}
                placeholder={placeholder}
                editorState={editorState}
                keyBindingFn={keyBindingFn}
                handleKeyCommand={handleKeyCommand}
                onChange={setEditorState}
                handlePastedFiles={handlePastedFiles}
              />
              {jsxBackend.render(editorComponents)}
            </div>
            <div className={classes.attachIconsWrapper}>
              {editorControls.map(item =>
                jsxBackend.render({
                  component: item.as,
                  props: {
                    key: item.as,
                    classes,
                    previewRef,
                    filesUploadRef: ref,
                    onStickerClick,
                    control: ComposerControl,
                    editorRef,
                    rid,
                    disableSubmit,
                    handleSubmit: () => handleKeyCommand('composer-submit')
                  }
                })
              )}
            </div>
          </div>
        </div>
      </div>
      {reactMode === 'edit' && 0 < photoId ? (
        <div>
          <div className={classes.extraDataPhoto}>
            <Image src={full_path} alt={'photo'} />
          </div>
          <Tooltip title={i18n.formatMessage({ id: 'remove' })}>
            <IconButton onClick={() => setPhotoId(undefined)}>
              <LineIcon icon="ico-close" />
            </IconButton>
          </Tooltip>
        </div>
      ) : null}
      {reactMode === 'edit' && 0 < stickerId ? (
        <div>
          <div className={classes.extraDataSticker}>
            <Image
              src={full_path}
              alt={'sticker'}
              aspectRatio={'fixed'}
              imageFit={'contain'}
            />
          </div>
          <Tooltip title={i18n.formatMessage({ id: 'remove' })}>
            <IconButton onClick={() => setStickerId(undefined)}>
              <LineIcon icon="ico-close" />
            </IconButton>
          </Tooltip>
        </div>
      ) : null}
    </form>
  );
}

export default React.forwardRef<HandleComposer, ChatComposerProps>(
  ChatComposer
);
