import Editor from '@draft-js-plugins/editor';
import { extractLinks } from '@draft-js-plugins/linkify';
import { DialogContent, useDialog } from '@metafox/dialog';
import useComposerContext from '@metafox/feed/hooks/useComposerContext';
import {
  LinkShape,
  useDraftEditorConfig,
  useGlobal,
  useSession
} from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import useAddPhotoToStatusComposerHandler from '@metafox/photo/hooks/useAddPhotoToStatusComposerHandler';
import { isPhoto } from '@metafox/photo/utils';
import { TruncateText, UserAvatar } from '@metafox/ui';
import PrivacyView from '@metafox/ui/PrivacyView';
import { editorStateToText } from '@metafox/utils';
import { useMediaQuery, CircularProgress } from '@mui/material';
import { styled, useTheme } from '@mui/material/styles';
import clsx from 'clsx';
import { EditorState, SelectionState } from 'draft-js';
import { concat, get, isEmpty, isObject, orderBy, uniq } from 'lodash';
import React, { useCallback, useEffect, useState } from 'react';
import composerConfig from '../../composerConfig';
import PrivacyControl from './PrivacyControl';
import AsPageAction from './AsPageAction';

const APP_GROUP = 'group';
const APP_PAGE = 'page';

const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(({ theme }) => ({
  marginRight: theme.spacing(1.5)
}));

const ComposerWrapper = styled('div', { name: 'ComposerWrapper' })(
  ({ theme }) => ({
    display: 'flex',
    alignItems: 'flex-start',
    padding: theme.spacing(0, 0, 0, 2),
    minWidth: 'calc(100% - 110px)'
  })
);

const CHARACTER_LIMIT_BACKGROUND_STATUS = 150;
const LINES_LIMIT_BACKGROUND_STATUS = 3;

interface Props {
  viewTypePrivacy?: boolean;
  hidePrivacy: boolean;
  parentIdentity?: string;
  parentType?: string;
  disabledPrivacy?: boolean;
}

const ComposerContent = ({
  viewTypePrivacy,
  hidePrivacy,
  parentIdentity,
  parentType,
  disabledPrivacy
}: Props) => {
  const theme = useTheme();
  const isSmallScreen = useMediaQuery(theme.breakpoints.down('sm'));
  const { jsxBackend, i18n, dispatch, dialogBackend, getSetting, useGetItem } =
    useGlobal();
  const parentUser = useGetItem(parentIdentity);
  const { user: authUser } = useSession();
  const { dialogProps } = useDialog();
  const setting = getSetting() as any;
  const {
    data: initData,
    classes,
    composerState,
    composerRef,
    editorRef,
    editorState,
    condition,
    setEditorState,
    isEdit,
    strategy,
    editor,
    asPage,
    setAsPage
  } = useComposerContext();

  const [editorPlugins, editorComponents, editorControls] =
    useDraftEditorConfig(composerConfig, condition, parentUser);

  const [onChangeFile] = useAddPhotoToStatusComposerHandler(composerRef);
  const [errorLink, setErrorLink] = useState<string>();
  const [loading, setLoading] = useState<boolean>(false);

  const placeholder = i18n.formatMessage(
    {
      id: composerState?.parentUser
        ? 'write_something_to_parent_user'
        : 'what_s_your_mind'
    },
    { user: composerState?.parentUser?.name }
  );

  useEffect(() => {
    setImmediate(() => focusToEndText());
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [dialogProps.open]);

  useEffect(() => {
    if (composerState.attachmentType === 'backgroundStatus') {
      const length = editorStateToText(editorState).length;
      const lines = editorStateToText(editorState)?.split(/\r\n|\r|\n/).length;

      if (
        length > CHARACTER_LIMIT_BACKGROUND_STATUS ||
        lines > LINES_LIMIT_BACKGROUND_STATUS
      ) {
        composerRef.current.hideBackground();
      } else {
        composerRef.current.displayBackground();
      }
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [editorState]);

  useEffect(() => {
    composerRef.current.setPostAsPage(asPage);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [asPage]);

  useEffect(() => {
    if (isEmpty(initData) || isEmpty(initData.attachmentType) || isEdit) return;

    const { attachmentType } = initData;

    const attachment = initData.attachments[attachmentType];

    if (isEmpty(attachment)) return;

    const { value, as } = initData.attachments[attachmentType];
    const valueComposer =
      composerState.attachments[attachmentType]?.value || [];

    const newAttachment = uniq(concat(valueComposer, value)).filter(Boolean);

    if (
      composerState.attachmentType === 'backgroundStatus' &&
      attachmentType !== 'backgroundStatus'
    ) {
      composerRef.current.removeBackground();
    }

    composerRef.current.setAttachments(attachmentType, attachmentType, {
      as,
      value: attachmentType === 'poll' ? value : newAttachment
    });

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [composerRef, initData]);

  const moveSelectionToEnd = (editorState: EditorState) => {
    const content = editorState.getCurrentContent();
    const blockMap = content.getBlockMap();

    const key = blockMap.last().getKey();
    const length = blockMap.last().getLength();

    const selection = new SelectionState({
      anchorKey: key,
      anchorOffset: length,
      focusKey: key,
      focusOffset: length
    });

    return selection;
  };

  const focusToEndText = () => {
    setEditorState(
      EditorState.forceSelection(editorState, moveSelectionToEnd(editorState))
    );
    editorRef.current.focus();
  };

  const focusEditor = () => {
    editorRef.current.focus();
  };

  const setPrivacyValue = useCallback(
    (value: unknown) => {
      composerRef.current.setPrivacy(value);
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const handlePreviewLink = (editorStateProp: EditorState) => {
    if (!isEmpty(initData.attachments)) return;

    const currentText = editorStateToText(editorState);
    const nextText = editorStateToText(editorStateProp);

    if (
      currentText === nextText ||
      (Object.keys(composerState.attachments).length &&
        !composerState.attachments['link']) ||
      (isEdit && composerState.attachments['link'])
    )
      return;

    const links = extractLinks(nextText);

    if (!links) return;

    if (
      links[links.length - 1].url ===
        composerState.attachments['link']?.value?.link ||
      links[links.length - 1].url === errorLink
    )
      return;

    setLoading(true);

    dispatch({
      type: 'statusComposer/getLink',
      payload: links[links.length - 1].url,
      meta: {
        onSuccess: (data: LinkShape) => {
          composerRef.current.setAttachments('link', 'link', {
            as: 'StatusComposerControlPreviewLink',
            value: data
          });
          setErrorLink(undefined);
          setLoading(false);
        },
        onFailure: (data: string) => {
          setErrorLink(data);

          composerState.attachments['link'] &&
            composerRef.current.removeAttachments();
          setLoading(false);
        }
      }
    });
  };

  const handleChangeCompose = (editorState: EditorState) => {
    handlePreviewLink(editorState);
    setEditorState(editorState);
  };

  const checkCanPastePhoto = (files: Blob[]) => {
    const isAllowVideo = get(setting, 'feed.types.video.can_create_feed');
    const isAllowPhoto = get(setting, 'feed.types.photo_set.can_create_feed');

    const valid = isPhoto(files, isAllowVideo, isAllowPhoto);

    return (
      (!composerState.attachmentType ||
        composerState.attachmentType === 'photo') &&
      valid
    );
  };

  const handlePastedFiles = (files: Blob[]) => {
    if (checkCanPastePhoto(files)) {
      onChangeFile(files);
    } else {
      dialogBackend.alert({
        message: i18n.formatMessage({ id: 'cant_add_attachment' })
      });
    }

    return 'not-handled';
  };

  const scrollProps = isSmallScreen ? { autoHeightMax: 'none' } : {};

  return (
    <DialogContent className={classes.root}>
      <div className={classes.infoWrapper}>
        {parentUser?.item_type === APP_PAGE &&
        !isEdit &&
        parentUser?.is_admin ? (
          <AsPageAction
            asPage={asPage}
            setAsPage={setAsPage}
            page={parentUser}
          />
        ) : (
          <>
            <AvatarWrapper>
              <UserAvatar user={authUser as any} size={48} />
            </AvatarWrapper>
            <div>
              <div className={classes.userName}>
                <TruncateText
                  lines={1}
                  variant="h5"
                  className={classes.userName}
                >
                  {initData?.parentUser?.item_type === APP_PAGE
                    ? initData.parentUser.name
                    : authUser?.full_name}
                </TruncateText>
              </div>
              <div className={classes.buttonWrapper}>
                {!hidePrivacy && (
                  <div className={classes.privacyButton}>
                    <PrivacyControl
                      disabled={
                        Boolean(initData?.parentUser) || disabledPrivacy
                      }
                      setValue={setPrivacyValue}
                      value={composerState.privacy}
                    />
                  </div>
                )}
                {viewTypePrivacy && (
                  <div className={classes.privacyButton}>
                    <PrivacyView item={composerState.privacy_detail} />
                  </div>
                )}
                {initData?.parentUser?.item_type === APP_GROUP && (
                  <div className={classes.parentInfo}>
                    <TruncateText className={classes.parentName} lines={1}>
                      {composerState.parentUser?.name}
                    </TruncateText>
                  </div>
                )}
              </div>
            </div>
          </>
        )}
      </div>
      <ScrollContainer autoHide autoHeight {...scrollProps}>
        <div className={clsx(classes.contentWrapper, composerState.className)}>
          <ComposerWrapper>
            <div className={classes.composeInner}>
              <div
                className={clsx(classes.composer, composerState.className)}
                style={composerState.editorStyle}
                onClick={focusEditor}
                data-testid="fieldStatus"
              >
                <Editor
                  handlePastedFiles={handlePastedFiles}
                  ref={editorRef}
                  textAlignment={composerState.textAlignment}
                  editorState={editorState}
                  plugins={editorPlugins}
                  placeholder={placeholder}
                  onChange={handleChangeCompose}
                />
              </div>
            </div>
          </ComposerWrapper>
          <div className={classes.attachIconsWrapper}>
            {editorControls.map(item =>
              jsxBackend.render({
                component: item.as,
                props: {
                  disabled: item.disabled,
                  key: item.as,
                  strategy,
                  classes,
                  editorRef,
                  composerRef,
                  value: editor
                }
              })
            )}
          </div>
        </div>
        <div className={classes.editorComponentsWrapper}>
          {jsxBackend.render(editorComponents)}
        </div>
        <div className={classes.attachmentStage}>
          {isObject(composerState.attachments) &&
            Object.values(composerState.attachments).map(
              (attachment: any) =>
                attachment &&
                jsxBackend.render({
                  component: attachment.as,
                  props: {
                    key: attachment.as,
                    value: attachment.value,
                    composerRef,
                    editorRef,
                    hideRemove: isEdit && !!initData.attachmentType,
                    isEdit
                  }
                })
            )}
          {loading === true ? (
            <div className={classes.loading}>
              <CircularProgress size={30} />
            </div>
          ) : null}
        </div>
      </ScrollContainer>
      <div className={classes.tagsStage}>
        {orderBy(Object.values(composerState.tags), 'priority').map(
          (data: any) =>
            jsxBackend.render({
              component: data.as,
              props: {
                key: data.as,
                value: data.value,
                composerRef,
                editorRef,
                parentType,
                parentIdentity
              }
            })
        )}
      </div>
    </DialogContent>
  );
};

export default ComposerContent;
