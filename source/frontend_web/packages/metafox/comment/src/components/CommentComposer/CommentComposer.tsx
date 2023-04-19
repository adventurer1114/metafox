/**
 * @type: ui
 * name: CommentComposer
 * chunkName: composer
 */
import Editor from '@draft-js-plugins/editor';
import {
  RefOf,
  useDraftEditorConfig,
  useGetItem,
  useGlobal,
  useScript,
  useResourceAction
} from '@metafox/framework';
import { Image, LineIcon, UserAvatar } from '@metafox/ui';
import {
  editorStateToText,
  getImageSrc,
  htmlToText,
  textToRaw,
  isPhotoType
} from '@metafox/utils';
import { Box, styled, Tooltip } from '@mui/material';
import {
  convertFromRaw,
  EditorState,
  getDefaultKeyBinding,
  Modifier,
  SelectionState,
  convertToRaw
} from 'draft-js';
import React from 'react';
import composerConfig from '../../composerConfig';
import { CommentComposerProps, PreviewUploadPhotoHandle } from '../../types';
import PreviewUploadPhoto from '../PreviewUploadPhoto';
import CommentControl from './CommentControl';
import { isEmpty, get } from 'lodash';
import { useDispatch } from 'react-redux';

const name = 'CommentComposer';

const ComposeOuter = styled('div', {
  name,
  slot: 'composeOuter',
  shouldForwardProp: prop => prop !== 'margin' && prop !== 'processing'
})<{ margin: string; processing?: boolean }>(
  ({ theme, margin, processing }) => ({
    cursor: 'default',
    display: 'flex',
    padding: theme.spacing(2, 0),
    ...(margin && { padding: theme.spacing(1, 0) }),
    ...(margin === 'none' && { padding: 0 }),
    ...(processing && { pointerEvents: 'none', opacity: 0.5 })
  })
);
const ComposeInputWrapper = styled('div', {
  name,
  slot: 'composeInputWrapper'
})(({ theme }) => ({
  width: '100%',
  border:
    theme.palette.mode === 'light'
      ? theme.mixins.border('secondary')
      : 'solid 1px rgba(73, 73, 73, 0.2)',
  backgroundColor: theme.palette.action.hover,
  minHeight: theme.spacing(4),
  borderRadius: theme.spacing(2),
  display: 'flex',
  flexFlow: 'wrap',
  transition: 'all 200ms ease 0s',
  [theme.breakpoints.down('sm')]: {
    '.activeSend &': {
      width: 'calc(100% - 20px)'
    }
  }
}));
const Composer = styled('div', { name, slot: 'composer' })(({ theme }) => ({
  cursor: 'auto',
  flex: 1,
  flexBasis: 'auto',
  minWidth: 0,
  padding: '6px 8px 6px 12px',
  display: 'flex',
  '& .public-DraftEditorPlaceholder-root': {
    position: 'absolute',
    color: theme.palette.text.secondary
  },
  '& .DraftEditor-root': {
    width: '100%'
  },
  '& .DraftEditor-editorContainer': {
    minWidth: theme.spacing(2)
  },
  '& .mentionSuggestionsWrapper': {
    position: 'absolute',
    zIndex: 20,
    minWidth: 180,
    backgroundColor: theme.mixins.backgroundColor('paper'),
    borderRadius: 4,
    padding: theme.spacing(1),
    marginTop: theme.spacing(1),
    boxShadow:
      '0px 10px 13px -6px rgba(0,0,0,0.2), 0px 20px 31px 3px rgba(0,0,0,0.14)'
  },
  '& .mentionText': {
    color: theme.palette.primary.main
  },
  '& .hashtagStyle': {
    color: theme.palette.primary.main
  }
}));
const AttachIconsWrapper = styled('div', { name, slot: 'attachIconsWrapper' })(
  ({ theme }) => ({
    display: 'inline-flex',
    alignItems: 'flex-end',
    padding: theme.spacing(0, 0.25),
    marginLeft: 'auto'
  })
);
const IconSend = styled('div', { name, slot: 'IconSend' })(({ theme }) => ({
  marginLeft: '8px',
  display: 'none',
  color: theme.palette.primary.main,
  position: 'absolute',
  right: '0',
  top: '50%',
  transform: 'translateY(-40%)',
  [theme.breakpoints.down('sm')]: {
    '.activeSend &': {
      display: 'inline-block'
    }
  }
}));
const ComposeWrapper = styled('div', { name, slot: 'ComposeWrapper' })(() => ({
  position: 'relative'
}));
const CloseButton = styled('div', { name, slot: 'closeButton' })(
  ({ theme }) => ({
    cursor: 'pointer',
    width: theme.spacing(3),
    height: theme.spacing(3),
    backgroundColor:
      theme.palette.mode === 'light'
        ? theme.palette.grey['400']
        : theme.palette.background.default,
    borderRadius: '50%',
    fontSize: 'small',
    justifyContent: 'center',
    alignItems: 'center',
    display: 'inline-flex',
    opacity: 0.8,
    color:
      theme.palette.mode === 'light'
        ? theme.palette.text.primary
        : theme.palette.text.primary,
    '&:hover': {
      border: theme.mixins.border('secondary'),
      backgroundColor: theme.palette.background.default,
      color: theme.palette.text.primary
    }
  })
);
const CancelBtn = styled('div', { name, slot: 'cancelBtn' })(({ theme }) => ({
  fontSize: 'small',
  color: theme.palette.text.secondary,
  marginLeft: theme.spacing(5),
  marginBottom: theme.spacing(1),
  '& span': {
    color: theme.palette.primary.main,
    '&:hover': {
      textDecoration: 'underline'
    }
  }
}));
const AvatarWrapper = styled('div', { name, slot: 'avatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1)
  })
);

const useCaptcha = (name: string) => {
  const { getSetting } = useGlobal();
  const settingCaptcha = getSetting('captcha') as Record<string, any>;
  const condition = get(settingCaptcha.rules, name);
  const [loading, setLoading] = React.useState(false);
  const config = useResourceAction('captcha', 'captcha', 'getVerifyForm');
  const dispatch = useDispatch();

  const isGoogleCaptchaV3 = settingCaptcha?.default === 'recaptcha_v3';
  // const isImageCaptcha = settingCaptcha?.default === 'image_captcha';
  const siteKey = isGoogleCaptchaV3
    ? settingCaptcha?.recaptcha_v3?.site_key
    : null;

  useScript(
    isGoogleCaptchaV3 && siteKey && condition
      ? `https://www.google.com/recaptcha/api.js?render=${siteKey}`
      : null
  );

  if (!condition) {
    return {
      status: false
    };
  }

  const processCaptcha = props => {
    if (loading) return;

    setLoading(true);
    dispatch({
      type: 'comment/processCaptcha',
      payload: {
        settingCaptcha,
        setLoading,
        siteKey,
        ...props
      }
    });
  };

  return {
    type: settingCaptcha?.default,
    status: true,
    processing: loading,
    setProcessing: setLoading,
    processCaptcha,
    formDatasource: config
  };
};

function CommentComposer(
  {
    open,
    text = '',
    focus,
    editing,
    identity,
    identityResource,
    onCancel,
    extra_data,
    onSuccess,
    margin = 'normal',
    parentUser,
    isReply,
    replyUser,
    actions
  }: CommentComposerProps,
  ref: RefOf<HTMLButtonElement>
) {
  const {
    i18n,
    getAcl,
    getSetting,
    useSession,
    dispatch,
    jsxBackend,
    dialogBackend
  } = useGlobal();
  const { user: authUser } = useSession();
  const acl = getAcl();
  const setting = getSetting('comment') as Object;
  const captchaData = useCaptcha('comment.create_comment');
  const editorRef = React.useRef<Editor>();
  const previewRef = React.useRef<PreviewUploadPhotoHandle>();
  const isAuthReplyUser = replyUser?.id === authUser?.id;
  const [editorState, setEditorState] = React.useState<EditorState>(
    EditorState.createWithContent(
      convertFromRaw(textToRaw(htmlToText(text || '')))
    )
  );
  const [openSend, setOpenSend] = React.useState<Boolean>(false);
  const [photoId, setPhotoId] = React.useState<number>(
    'storage_file' === extra_data?.extra_type ? extra_data?.item_id : undefined
  );
  const [stickerId, setStickerId] = React.useState<number>(
    'sticker' === extra_data?.extra_type ? extra_data?.item_id : undefined
  );
  const commentWrapperRef = React.useRef();

  const sticker = useGetItem(`sticker.entities.sticker.${stickerId}`);

  const isShowPreviewSticker =
    sticker || (editing && !!stickerId && 'sticker' === extra_data?.extra_type);

  const isShowPreviewPhoto =
    editing &&
    extra_data?.item_id === photoId &&
    'storage_file' === extra_data?.extra_type;

  const hasExtraContent = isShowPreviewSticker || isShowPreviewPhoto || photoId;

  const focusEditor = React.useCallback(() => {
    // updating open and focus at the same time cause bug: plugin editor not works
    setImmediate(() => editorRef.current.focus());
  }, []);
  let placeholderMessage = isReply
    ? 'write_reply_three_dots'
    : 'write_comment_three_dots';

  if (parentUser?.resource_name === 'group' && parentUser?.reg_method === 0) {
    placeholderMessage = isReply
      ? 'write_public_reply_three_dots'
      : 'write_public_comment_three_dots';
  }

  const placeholder = i18n.formatMessage({ id: placeholderMessage });

  const clearEditorText = React.useCallback(() => {
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
        if (onSuccess) {
          onSuccess();
        }

        if (captchaData?.type) {
          clearEditor();

          if (captchaData?.type === 'image_captcha') {
            dialogBackend.dismiss();
          }
        }
      },
      onCancel: errorData => {
        if (onCancel) {
          onCancel();
        }
      }
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [onSuccess, onCancel, captchaData?.type]);

  const clearEditor = () => {
    clearEditorText();

    if (previewRef.current?.clear) {
      previewRef.current.clear();
    }

    setStickerId(undefined);
    setPhotoId(undefined);
  };

  const condition = React.useMemo(
    () => ({
      editing,
      acl,
      setting,
      hasExtraContent
    }),
    [acl, editing, hasExtraContent, setting]
  );
  const [remountKey, setRemountKey] = React.useState(1);
  const [editorPlugins, editorComponents, editorControls] =
    useDraftEditorConfig(composerConfig, condition);

  const onStickerClick = React.useCallback(
    (sticker_id: number) => {
      const text = editorStateToText(editorState);

      if (text || editing) {
        setStickerId(sticker_id);
        setPhotoId(null);

        return;
      }

      const handleSubmitSticker = (data = {}) => {
        dispatch({
          type: 'comment/composer/CREATE',
          payload: {
            editing,
            identity,
            sticker_id,
            text: undefined,
            identityResource,
            ...data
          },
          meta
        });
      };

      if (captchaData?.status && captchaData?.processCaptcha && !editing) {
        if (captchaData.type === 'image_captcha') {
          setStickerId(sticker_id);
        }

        handleCaptcha(handleSubmitSticker);

        return;
      }

      handleSubmitSticker();
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [dispatch, editing, editorState, identity, identityResource]
  );

  const submitComment = (data = {}, clear = true) => {
    const sticker_id = stickerId;

    if (clear) {
      clearEditor();
    }

    const text = editorStateToText(editorState).trim();

    dispatch({
      type: editing ? 'comment/composer/UPDATE' : 'comment/composer/CREATE',
      payload: {
        editing,
        identity,
        text,
        sticker_id,
        photo_id: photoId,
        identityResource,
        ...data
      },
      meta
    });
  };

  const handleCaptcha = onSubmitCaptcha => {
    captchaData?.processCaptcha({
      handleSubmit: onSubmitCaptcha
    });

    return;
  };

  const handleSubmit = () => {
    if (previewRef.current?.checkIsLoading()) return;

    const text = editorStateToText(editorState).trim();

    if (!text && !photoId && !stickerId && !editing) return;

    if (editing && !text && !stickerId && !photoId) {
      actions.deleteComment();

      return;
    }

    if (captchaData?.status && captchaData?.processCaptcha && !editing) {
      handleCaptcha(submitComment);

      return;
    }

    submitComment();
  };

  const handlePreviewPhoto = (id: number) => {
    setPhotoId(id);
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
      handleSubmit();

      return 'handled';
    }

    return 'not-handled';
  };

  React.useEffect(() => {
    if (focus) {
      focusEditor();

      if (isReply && replyUser && !isAuthReplyUser) {
        const mentionField = createFieldMention(replyUser);

        if (mentionField) {
          setEditorState(mentionField);
          setImmediate(() => {
            focusEditor();
          });
        }
      }
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [focus, focusEditor, replyUser?.id]);

  React.useEffect(() => {
    if (editorState && !editorState.getDecorator()) {
      const newValue = remountKey + 1;
      setRemountKey(newValue);
    }

    setOpenSend(false);

    if (editorStateToText(editorState) !== '') {
      setOpenSend(true);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [editorState]);

  const createFieldMention = (data: Record<string, any>) => {
    const content = editorState.getCurrentContent();

    const contentState = convertToRaw(content);
    const models = contentState.entityMap;
    const blocks = contentState.blocks;
    const plainText = content.getPlainText();
    const lengthMention =
      blocks.length &&
      blocks.length === 1 &&
      blocks[0].entityRanges.length &&
      blocks[0].entityRanges.length === 1
        ? blocks[0].entityRanges[0]?.length
        : 0;
    const plainTextWithoutMention =
      Object.values(models).length < 2
        ? plainText.slice(lengthMention)
        : plainText;

    if (isEmpty(data) || plainTextWithoutMention.trim().length) return null;

    const selection = editorState.getSelection().merge({
      anchorKey: content.getFirstBlock().getKey(),
      anchorOffset: 0,

      focusOffset: content.getLastBlock().getText().length,
      focusKey: content.getLastBlock().getKey()
    });
    const contentStateWithEntity = content.createEntity(
      'mention',
      'IMMUTABLE',
      {
        mention: {
          name: data.full_name,
          link: `@mention/${data.resource_name}/${data.id}`
        }
      }
    );
    const entityKey = contentStateWithEntity.getLastCreatedEntityKey();
    const textWithEntity = Modifier.replaceText(
      content,
      selection,
      data.full_name,
      null,
      entityKey
    );
    const textWithEntitySpace = Modifier.insertText(
      textWithEntity,
      textWithEntity.getSelectionAfter(),
      ' '
    );

    const newState = EditorState.createWithContent(textWithEntitySpace);

    return EditorState.forceSelection(
      newState,
      textWithEntitySpace.getSelectionAfter()
    );
  };

  const handlePastedFiles = (files: FileList) => {
    if (!files.length) return;

    // accept image
    if (previewRef && previewRef.current) {
      const file = files[0];

      if (isPhotoType(file.type)) {
        const data: FileList = [file];
        previewRef.current?.attachFiles(data);
      }
    }
  };

  React.useEffect(() => {
    const handleESC = event => {
      if (onCancel && 27 === event.keyCode) {
        onCancel();
      }
    };

    window.addEventListener('keydown', handleESC);

    return () => {
      window.removeEventListener('keydown', handleESC);
    };
  }, [onCancel]);

  React.useEffect(() => {
    if (captchaData.status) {
      captchaData?.setProcessing(false);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [captchaData.status]);

  if (!open) {
    return null;
  }

  return (
    <form role="presentation" data-testid="commentForm">
      <ComposeOuter margin={margin}>
        <AvatarWrapper>
          <UserAvatar
            user={authUser as any}
            size={32}
            data-testid="userAvatar"
          />
        </AvatarWrapper>
        <Box flex="1" minWidth="0">
          <ComposeWrapper
            className={openSend || photoId || stickerId ? 'activeSend' : ''}
          >
            <ComposeInputWrapper ref={commentWrapperRef}>
              <Composer onClick={focusEditor} data-testid="fieldComment">
                <Editor
                  key={remountKey}
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
              </Composer>
              <AttachIconsWrapper>
                {editorControls.map(item =>
                  jsxBackend.render({
                    component: item.as,
                    props: {
                      key: item.as,
                      previewRef,
                      onStickerClick,
                      control: CommentControl,
                      editorRef
                    }
                  })
                )}
              </AttachIconsWrapper>
            </ComposeInputWrapper>
            <IconSend onClick={handleSubmit}>
              <LineIcon icon="ico-paperplane-alt-o" />
            </IconSend>
          </ComposeWrapper>
          {isShowPreviewPhoto && (
            <Box mt={1}>
              <Box maxWidth={208} position="relative">
                <Image
                  src={getImageSrc(extra_data?.image, '500')}
                  alt={'photo'}
                />
                <Box position="absolute" top={1} right={1}>
                  <Tooltip title={i18n.formatMessage({ id: 'remove' })}>
                    <CloseButton onClick={() => setPhotoId(null)}>
                      <LineIcon icon="ico-close" />
                    </CloseButton>
                  </Tooltip>
                </Box>
              </Box>
            </Box>
          )}
          {isShowPreviewSticker && (
            <Box mt={1}>
              <Box position="relative" width={80} height={80}>
                <Image
                  src={getImageSrc(sticker?.image || extra_data?.image, '500')}
                  alt={'sticker'}
                  aspectRatio={'fixed'}
                  imageFit={'contain'}
                />
                <Box position="absolute" top={0} right={-1.5}>
                  <Tooltip title={i18n.formatMessage({ id: 'remove' })}>
                    <CloseButton onClick={() => setStickerId(0)}>
                      <LineIcon icon="ico-close" />
                    </CloseButton>
                  </Tooltip>
                </Box>
              </Box>
            </Box>
          )}
          <PreviewUploadPhoto ref={previewRef} onChange={handlePreviewPhoto} />
        </Box>
      </ComposeOuter>

      {editing && (
        <CancelBtn onClick={onCancel}>
          {i18n.formatMessage(
            { id: 'press_esc_to_cancel' },
            {
              button: text => <span role="button" children={text} />
            }
          )}
        </CancelBtn>
      )}
    </form>
  );
}

export default React.forwardRef<HTMLButtonElement, CommentComposerProps>(
  CommentComposer
);
